<?php

    global $text, $maxchar, $end;
    function substrwords($text, $maxchar, $end='...') {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } else {
            $output = $text;
        }
        return $output;
    }

    $rss = new DOMDocument();
    $rss->load('https://blog.balidiving.com/feed/'); // <-- Change feed to your site
    $feed = array();
    foreach ($rss->getElementsByTagName('item') as $node) {
        $item = array (
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
            'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
            'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,

        );
        array_push($feed, $item);
    }

    $limit = 3; // <-- Change the number of posts shown
    for ($x=0; $x<$limit; $x++) {
        $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
        $link = $feed[$x]['link'];
        $description = $feed[$x]['desc'];
        $description = substrwords($description, 100);
        $date = date('l F d, Y', strtotime($feed[$x]['date']));

        echo '<h3 style="color:#003C84" class="tebal">'.$title.'</h3>';
        //echo '<p><small><em>Posted on '.$date.'</em></small></p>';
        //echo ''; //untuk thumbnail jika script bisa di eksekusi
        echo '<p style="color:#063445">'.$description.'<a href="'.$link.'" style="color:#149ED0"><strong>Read More</strong></a></p>';
    }
?>
