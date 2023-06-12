<?php
session_start();
if (isset($_POST['redirect'])) {
    session_unset();
    $_SESSION['user'] = $_POST['user'];
    $_SESSION['image_name'] = $_POST['image_name'];
    unset($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap');
        </style>
    
        <link rel="stylesheet" href="styles.css">

        <title>Docker Console</title>
    </head>
    <body>
        <div class="navbar">
            <div class="navbar-item">
                <p>Welcome <?php echo $_SESSION['user']?></p>
            </div>
        </div>
        <p id="image_name"><?php echo $_SESSION['image_name']?></p>
        <p id="running">Status: <?php echo $_SESSION['status']?></p>
        <form class="form" action="/handledata.php" method="post">
            <label for="startDocker" id="image_name_label"></label>
            <input name="image_name" type="hidden" id="image_name_input">
            <input name="user" type="hidden" id="user_input">
            <button id="docker_start" type="submit" name="docker-start">Start</button>
            <button id="docker_stop" type="submit" name="docker-stop">Stop</button>
            <button id="docker_extend" type="submit" name="docker-extend">Extend Time</button>
        </form>
        <p>Mission Address: <?php echo $_SESSION['ipaddr'];?></p>
        <p id="time_left">Time Left:</p>
        <br><br>
        <p>Docker controls: Kali</p>
        <p id="running">Status: <?php echo $_SESSION['statuskali']?></p>
        <form id="kali" class="form" action="/handledata.php" method="post">
            <input name="image_name" type="hidden" value="kali">
            <input name="user" type="hidden" id="user_input_kali">
            <button id="kali_start" type="submit" name="docker-start">Start</button>
            <button id="kali_stop" type="submit" name="docker-stop">Stop</button>
            <button id="kali_extend" type="submit" name="kali-extend">Extend Time</button>
        </form>
        <p>Kali Address: <?php echo $_SESSION['ipaddrkali'];?></p>
        <a id="vnc_address">VNC Address: </a>
        <p id="time_left_kali">Time Left:</p>
        <br><br>
        <h3>Please do not refresh this page</h3>
    </body>
</html>
<script>

    var docker_name = document.getElementById("image_name").innerHTML;
    var username = "<?php echo $_SESSION['user']; ?>";
    docker_name = docker_name.replace(/%20/g, "");
    document.getElementById("image_name").innerHTML = "Image name: " + docker_name;
    document.getElementById("image_name").innerHTML = "Docker controls: " + docker_name;
    document.getElementById("image_name_input").setAttribute('value', docker_name);
    document.getElementById("user_input").setAttribute('value', username);
    document.getElementById("user_input_kali").setAttribute('value', username);

    printVNCAddress();
    handleKaliTimer();
    handlerDockerTimer();
    extendDockerTimer();

    function printVNCAddress() {
        var kaliIP = "<?php echo $_SESSION['ipaddrkali'];?>";
        if (kaliIP != "") {
            var address = "https://" + kaliIP + ":8080/vnc.html";
            document.getElementById("vnc_address").innerHTML = "VNC Address: " + address;
            document.getElementById("vnc_address").setAttribute('href', address);
            document.getElementById("vnc_address").setAttribute('target', "_blank");
        }
    }

    function handleKaliTimer() {
        var kaliIP = "<?php echo $_SESSION['ipaddrkali'];?>";
        if (kaliIP != "") {
            if (sessionStorage.getItem("time_left_kali") == null) {
                sessionStorage.setItem("time_left_kali", 60);
            }
            document.getElementById("time_left_kali").innerHTML = "Time Left: " + sessionStorage.getItem("time_left_kali") + " Minutes";
            countdown("time_left_kali");
        }
        else {
            sessionStorage.removeItem("time_left_kali");
        }
    }

    function handlerDockerTimer() {
        var dockerIP = "<?php echo $_SESSION['ipaddr'];?>";
        if (dockerIP!= "") {
            if (sessionStorage.getItem("time_left") == null) {
                sessionStorage.setItem("time_left", 60);
            }
            document.getElementById("time_left").innerHTML = "Time Left: " + sessionStorage.getItem("time_left") + " Minutes";
            countdown("time_left");
        }
        else {
            sessionStorage.removeItem("time_left");
        }
    }

    function extendDockerTimer() {
        var extend = "<?php echo $_SESSION['extend_time'];?>";
        if (extend == true) {
            if (sessionStorage.getItem("time_left") != null) {
                sessionStorage.setItem("time_left", 60);
                <?php $_SESSION['extend_time'] = false;?>
                document.getElementById("time_left").innerHTML = "Time Left: " + sessionStorage.getItem("time_left") + " Minutes";
                countdown("time_left");
            }
        }
    }
    function extendKaliTimer() {
        var extend = "<?php echo $_SESSION['extend_time_kali'];?>";
        if (extend == true) {
            if (sessionStorage.getItem("time_left_kali") != null) {
                sessionStorage.setItem("time_left_kali", 60);
                <?php $_SESSION['extend_time_kali'] = false;?>
                document.getElementById("time_left_kali").innerHTML = "Time Left: " + sessionStorage.getItem("time_left_kali") + " Minutes";
                countdown("time_left_kali");
            }
        }
    }


    function countdown(elementName) {
        var internalintervalID = setInterval(function () {
            console.log("in interval function")
            sessionStorage.setItem(elementName, sessionStorage.getItem(elementName) - 1);
            document.getElementById(elementName).innerHTML = "Time Left: " + sessionStorage.getItem(elementName) + " Minutes";
            if (sessionStorage.getItem(elementName) == 0) {
                clearInterval(internalintervalID);
                if (elementName.includes("kali")) {
                    var button = document.getElementById("kali_stop");
                    button.click();
                }
                else {
                    var button = document.getElementById("docker_stop");
                    button.click();
                }
            }
        }, 60000);
        return internalintervalID;
    }

    // window.addEventListener('beforeunload', function (e) {
    //     var confirmationMessage = 'It looks like you have been editing something. '
    //                         + 'If you leave before saving, your changes will be lost.';

    //     (e || window.event).returnValue = confirmationMessage; //Gecko + IE
    //     return confirmationMessage
    // });

</script>
