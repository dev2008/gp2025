<?php
if (!defined('custom_page_from_inclusion')) { 
    die(); 
}

// Fetch the list of potential targets from the database and sort by a_league and n_offcoach
try {
    $sql = "SELECT DISTINCT a_league, n_offcoach 
            FROM n_playbyplay 
            WHERE n_offcoach IS NOT NULL AND n_offcoach <> ''
            ORDER BY a_league ASC, n_offcoach ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$targets) {
        throw new Exception('No potential targets found.');
    }

    // Display the dropdown list of targets
    echo "<form method='post'>";
    echo "<select name='target'>";
    foreach ($targets as $target) {
        echo "<option value='" . $target['a_league'] . ":" . $target['n_offcoach'] . "'>";
        echo "League: " . $target['a_league'] . " | Coach: " . $target['n_offcoach'];
        echo "</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='Select Target'>";
    echo "</form>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Once a target is selected, retrieve and process the data with the last 5 seasons filter and only records where a_down = 1
if (isset($_POST['target'])) {
    try {
        list($selectedleague, $selectedcoach) = explode(':', $_POST['target']);

        // Get the last 5 seasons (assuming 'a_season' is a numerical column)
        $sql = "SELECT MAX(a_season) as max_season FROM n_playbyplay WHERE a_league = :selectedleague";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['selectedleague' => $selectedleague]);
        $maxSeasonRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$maxSeasonRow || !isset($maxSeasonRow['max_season'])) {
            throw new Exception('Unable to determine the maximum season.');
        }

        $maxSeason = $maxSeasonRow['max_season'];
        $minSeason = $maxSeason - 4; // Calculate the last 5 seasons

        // Fetch the play-by-play data for the last 5 seasons for the selected coach and league, filtering by a_down = 1
        $sql = "SELECT a_season, a_week, a_off, a_minutes, a_field, a_down, a_distance, a_form, a_ocall 
                FROM n_playbyplay 
                WHERE n_offcoach = :selectedcoach 
                AND a_league = :selectedleague
                AND a_season BETWEEN :minSeason AND :maxSeason
                AND a_down = 1";  // Only retrieve records where a_down = 1
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'selectedcoach' => $selectedcoach, 
            'selectedleague' => $selectedleague,
            'minSeason' => $minSeason,
            'maxSeason' => $maxSeason
        ]);
        $playData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Report the number of records retrieved
        $recordCount = count($playData);
        echo "<h3>Records Retrieved: $recordCount</h3>";

        if (!$playData) {
            throw new Exception('No play-by-play data found for the selected target in the last 5 seasons.');
        }

        // Prepare the data for OpenAI analysis, focusing on offense only and using two-letter codes for plays
        $formattedData = json_encode($playData);

        // Call OpenAI for analysis
        $ch = curl_init();
        
        $dataForAI = json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a professional football defensive coordinator analyzing offensive play data. The following information only includes offensive plays where a_down = 1. Please use only the two-letter code for playcalls without expanding or guessing their names. Stick strictly to the codes. If you need details on the playcalls, they can be found here: https://files.gameplan.org.uk/Gameplan_Playbook.pdf.'],
                ['role' => 'user', 'content' => "Analyze the following offensive play data: $formattedData"]
            ],
            'temperature' => 0.7,
            'max_tokens' => 500
        ]);
        
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataForAI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        $aiResponse = json_decode($response, true);

        // Debugging: Output the raw response from OpenAI for inspection
        echo "<h3>Raw OpenAI Response:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";

        if (!isset($aiResponse['choices'])) {
            throw new Exception('Invalid response from OpenAI API.');
        }

        // Display the AI's gameplan suggestions
        echo "<h3>AI Gameplan Suggestions:</h3>";
        foreach ($aiResponse['choices'] as $choice) {
            echo "AI Suggestion: " . $choice['message']['content'] . "<br>";
        }

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
