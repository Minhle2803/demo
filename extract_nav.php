<?php

// Read the source file
$file = file_get_contents("d:/project/demo/resources/views/pages/landing4.blade.php");

// Find navI18n section start
$navStart = strpos($file, '"navI18n": {');
$navContentStart = strpos($file, '{', $navStart);

// Find matching closing brace by counting braces
$depth = 0;
$pos = $navContentStart;
$len = strlen($file);
while ($pos < $len) {
    $ch = $file[$pos];
    if ($ch === '{') {
        $depth++;
    } elseif ($ch === '}') {
        $depth--;
        if ($depth === 0) {
            $navEnd = $pos;
            break;
        }
    }
    $pos++;
}

// Extract inner content (without outer braces)
$innerContent = substr($file, $navContentStart + 1, $navEnd - $navContentStart - 1);

// Trim trailing comma and whitespace
$innerContent = rtrim($innerContent);
if (substr($innerContent, -1) === ',') {
    $innerContent = substr($innerContent, 0, -1);
}

// Now parse: each key-value pair
// Pattern: whitespace "key": value,
preg_match_all('/\s*"([^"]+)"\s*:\s*(.+?)(?=\n\s*"(?:[^"]+)"\s*:|\n\s*\})/s', "\n" . $innerContent, $matches, PREG_SET_ORDER);

echo "Found " . count($matches) . " pairs via regex\n";

// Simpler: use line-by-line state machine
$lines = explode("\n", $innerContent);
$pairs = [];
$currentKey = null;
$currentValue = '';
$valueDepth = 0;

foreach ($lines as $line) {
    // Check if this line starts a new key
    if (preg_match('/^\s*"([^"]+)"\s*:\s*(.*)/', $line, $m) && $valueDepth === 0) {
        // Save previous pair
        if ($currentKey !== null) {
            $currentValue = rtrim($currentValue);
            if (substr($currentValue, -1) === ',') {
                $currentValue = substr($currentValue, 0, -1);
            }
            $pairs[$currentKey] = $currentValue;
        }
        $currentKey = $m[1];
        $currentValue = $m[2];

        // Check if the value continues (multiline string)
        $valueDepth = 0;
        $inString = false;
        for ($i = 0; $i < strlen($currentValue); $i++) {
            if ($currentValue[$i] === '"' && ($i === 0 || $currentValue[$i-1] !== '\\')) {
                $inString = !$inString;
                if (!$inString) {
                    // Check for brace/bracket after string
                    $remaining = trim(substr($currentValue, $i + 1));
                }
            }
        }
    } else {
        // Continuation of previous value
        if ($currentKey !== null) {
            $currentValue .= "\n" . $line;
        }
    }
}

// Save last pair
if ($currentKey !== null) {
    $currentValue = rtrim($currentValue);
    if (substr($currentValue, -1) === ',') {
        $currentValue = substr($currentValue, 0, -1);
    }
    $pairs[$currentKey] = $currentValue;
}

echo "Parsed " . count($pairs) . " key-value pairs\n";

// Show first 5
$i = 0;
foreach ($pairs as $k => $v) {
    echo "$k => " . substr($v, 0, 60) . "...\n";
    $i++;
    if ($i >= 5) break;
}

// Write pairs to file
file_put_contents("d:/project/demo/navI18n_pairs.json", json_encode($pairs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
