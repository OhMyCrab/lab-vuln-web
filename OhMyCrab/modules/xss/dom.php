<?php
    require_once "../../includes/auth.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>DOM XSS (DVWA Style)</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>DOM XSS</h1>
            <h2>Hãy chọn sách bạn muốn</h2>
            <form name="allbook" method="GET">
                <select name="book">
                    <script>
                        var lang = decodeURI(window.location.search.toprop || window.location.search);

                        if (lang.indexOf("book=") >= 0) {
                            var res = lang.substring(lang.indexOf("book=") + 5);
                            
                            document.write("<option value='" + res + "'>" + res + "</option>");
                        } else {
                            document.write("<option value='OhMyCrab'>OhMyCrab</option>");
                        }
                    </script>
                    <option value="Foxie">Foxie</option>
                    <option value="Pophello">Pophello</option>
                    <option value="Naru">Naru</option>
                </select>
                <input type="submit" style="margin-top: 20px;" value="Chọn">
            </form>

            <div id="output" style="margin-top: 20px;"></div>
        </div>
        <div class="content">
            <button><a href="/OhMyCrab/modules/xss/fixed/dom_fix.php">Bản fix</a></button>
        </div>
    </body>
</html>