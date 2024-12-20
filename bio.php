<?php

// Helper function to check if a number is in the block.json file
function isBlockedNumber($number) {
    $blockFile = 'black.json';
    if (file_exists($blockFile)) {
        $blockData = json_decode(file_get_contents($blockFile), true);
        if (in_array($number, $blockData['blocked_numbers'])) {
            return true;
        }
    }
    return false;
}

// Function to generate a random NID of 10, 13, or 17 digits
function generateRandomNID() {
    $lengthOptions = [10, 13, 17];
    $nidLength = $lengthOptions[array_rand($lengthOptions)];
    $nid = '';
    for ($i = 0; $i < $nidLength; $i++) {
        $nid .= rand(0, 9);
    }
    return $nid;
}

// Generate a random date of birth where the person is at most 70 years old
function generateRandomDOB() {
    $year = rand(date("Y") - 70, date("Y") - 19); // DOB from 70 to 19 years ago
    $month = rand(1, 12);
    $day = rand(1, 28); // Simplifying day range to avoid invalid dates
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

function readJsonFile($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true);
    }
    return [];
}

function writeJsonFile($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Main functionality
if (isset($_GET['number'])) {
    $number = $_GET['number'];

    // Check if number is in block.json
    if (isBlockedNumber($number)) {
        // If blocked, send a custom response
        $response = [
            'message' => 'vag sala tor bap ar number a ar try Koris na'
        ];
    } elseif (ctype_digit($number) && strlen($number) == 11) {
        // Load bio.json and proceed with normal operations
        $bioFile = 'bio.json';
        $bioData = readJsonFile($bioFile);

        if (isset($bioData[$number])) {
            $nid = $bioData[$number]['nid'];
            $dob = $bioData[$number]['dob'];
        } else {
            $nid = generateRandomNID();
            $dob = generateRandomDOB();
            $bioData[$number] = ['nid' => $nid, 'dob' => $dob];
            writeJsonFile($bioFile, $bioData);
        }

        $response = [
            'API OWNER' => 'RA FI',
            'TG CHANNEL' => 'RNF_MODs',
            'number' => $number,
            'nid' => $nid,
            'dob' => $dob
        ];
    } else {
        $response = [
            'error' => 'Wrong Number'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    $response = [
        'error' => 'No number parameter provided'
    ];
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

?>
