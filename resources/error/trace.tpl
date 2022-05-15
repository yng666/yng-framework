<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $throwable->getMessage(); ?></title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: 宋体;
            border: none;
        }

        *::-webkit-scrollbar {
            width: 3px;
        }

        *::-webkit-scrollbar-thumb {
            border-radius: 5px;
            background-color: #333a41;
        }

        .content {
            border: 1px solid #d5d1d1;
            width: 70vw;
            margin: .5em auto
        }

        #content {
            width: 85%;
            padding: 1em;
            font-size: 1.2em;
            margin: 0;
            overflow-y: scroll;
            background-color: #ECECEC
        }

        .title {
            background-color: #333a41;
            line-height: 3em;
            padding: 0;
            min-height: 3em;
            color: white;
            font-weight: bold;
            word-break: break-all;
        }

        ul.title {
            margin: 0;
        }

        .title > li {
            font-size: .9em;
            font-weight: normal;
            width: 6.5em;
            cursor: pointer;
            display: inline-block;
        }

        .title > li:hover {
            background-color: #131517;
        }

        pre {
            margin-top: 0;
            padding: 0 1em;
            display: block;
            word-break: break-all;
            white-space: break-spaces;
        }

        li.file {
            padding: 0;
            color: #5a5757;
            border-bottom: 1px solid #d5d1d1;
            display: flex;
            justify-content: space-between;
            cursor: pointer;
        }

        li.file > .number {
            text-align: center;
            font-size: .9em;
            width: 15%;
            padding: 1em .5em;
            border-right: 3px solid #ECECEC
        }

        @media screen and (max-width: 500px) {
            .content {
                width: 95vw !important;
            }

            #status {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="content"
     style="margin-bottom: 1em; box-sizing: border-box; overflow-wrap: anywhere; word-break: break-all; word-wrap: break-word; ">
    <div style="padding: .5em 2.5em; color: white; background-color:#ff3300; ">
        <h3 style="color: white; margin: .5em 0; font-weight: normal"><?php echo get_class($throwable); ?></h3>
        <h3 style="margin: .5em 0;color: white;"><?php echo $throwable->getMessage(); ?></h3>
    </div>
    <div style="margin: 0 2.5em;">
        <p><b>File: </b><?php echo $throwable->getFile(); ?> +<?php echo $throwable->getLine(); ?></p>
        <p><b>Code: </b><?php echo $throwable->getCode(); ?></p>
    </div>
</div>
<div class="content">
    <ul class="title" style="text-align: center; list-style-type: none;">
        <li>Stack Trace</li>
        <li>Request</li>
        <li>Context</li>
    </ul>
    <div style="display: flex; justify-content: space-between; height: calc(100vh - 4em);">
        <aside style="width: 25%; border-right: 1px solid #d5d1d1; overflow-y: scroll; overflow-x: hidden">
            <ul style="list-style-type: none; padding: 0; margin: 0; width: 100%;">
                <?php
                $trace = $throwable->getTrace();
                $total = count($trace);
                for ($key = 0; $key <= count($trace); $key++) {
                if (false === isset($trace[$key]['file'])) {
                $total--;
                continue;
                }
                $errorFile = $trace[$key]['file'];
                $file = file($trace[$key]['file']);
                $line = $trace[$key]['line'];
                $function = $trace[$key]['function'];
                $num = $total - $key;
                $content = '';
                for ($i = $line - 15; $i > 0 && $i < $line + 15 && $i < count($file); $i++) {
                $code = $file[$i];
                $content .= '<span style="color: grey">' . str_pad((string)($i + 1), 3, ' ', STR_PAD_BOTH) . '</span>';
                if ($i + 1 == $line) {
                $code = '
                <text style="background-color: white">' . str_replace($function, '<span style="color: red">' . $function . '</span>',
                    $file[$i]) . '
                </text>
                ';
                }
                $content .= $code;
                }
                $content = htmlspecialchars($content, 3);
                echo "
                <li class='file' data-content='{$content}' data-id='{$num}'>
                    <div class=\"number\">{$num}</div>
                    <div style='font-size: .9em; word-wrap: break-word; width: 85%; box-sizing: border-box; padding: .5em;'>
                        <p style='margin: 0 0 .5em 0'>{$errorFile} +{$line}</p>
                        <b style='margin: 0;'>{$function}</b>
                    </div>
                </li>
                ";
                }
                ?>
            </ul>
        </aside>
        <pre id="content"></pre>
    </div>
    <script>
        const li = document.getElementsByClassName('file');
        window.onload = function () {
            li[0].click();
        }
        for (let i in li) {
            li[i].onclick = function () {
                this.style.backgroundColor = '#ececec';
                var id = this.getAttribute('data-id');
                var sb = this.parentNode.children;
                for (let j in sb) {
                    try {
                        if (sb[j].getAttribute('data-id') === id) {
                            continue;
                        }
                        sb[j].style.backgroundColor = 'white';
                    } catch (e) {

                    }
                }
                let text = this.getAttribute('data-content');
                let content = document.getElementById('content');
                content.innerHTML = text;
            }
        }
    </script>
</body>
</html>
