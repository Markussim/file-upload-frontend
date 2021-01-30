<!DOCTYPE html>
<html lang="sv">

<head>
    <?php include("../webutv/head_content.php"); ?>
</head>

<body>
    <div>
        <?php include("../webutv/header.php"); ?>
        <div id="marksism">Ladda upp</div>
        <div id="flex_thing">
            <div id="search_box">
                <div id="inner_search_box">
                    <div id="search_results">
                        <div class="dropzone" id="dropzone">
                            <div id="inner_dropzone"></div>
                            <input style="display: none;" type="text" value="Hello World" id="myInput"> <br>
                            <p id="text">Släpp eller tryck här för att ladda upp</p>
                            <p id="done"></p>
                            <input id="file-input" type="file" name="name" style="display: none;" />
                        </div>
                        <style>
                            .dropzone {
                                position: relative;
                                display: flex;
                                flex-direction: column;
                                height: 30vh;
                                width: 380px;
                                border: 2px solid #ccc;
                                color: grey;
                                text-align: center;
                                margin: auto;
                                background: linear-gradient(0deg, blue 0%, white 0%);
                                z-index: 1;
                                align-items: center;
                                justify-content: center;
                                cursor: pointer;
                                transition: color 0.2s, border 0.2s;
                            }

                            .dropzone.dragover {
                                border: 2px solid #000;
                                color: #000;
                            }

                            #inner_dropzone {
                                position: absolute;
                                background-color: #63a4f9;
                                bottom: 0;
                                height: 0;
                                width: 100%;
                                z-index: -1;
                                transition: all 1s;
                            }
                        </style>
                        <script>
                            function copyText() {

                                document.getElementById("myInput").select();

                                document.getElementById("myInput").setSelectionRange(0, document.getElementById("myInput").value.length);

                                document.execCommand('copy')

                                console.log("Tried to copy");
                            }

                            (function() {
                                var dropzone = document.getElementById('dropzone');

                                var inner_dropzone = document.getElementById('inner_dropzone');

                                var text = document.getElementById('text');

                                let MBps = 0;

                                let timeLeftHumanReadble

                                var upload = function(files) {
                                    var formData = new FormData();
                                    var xhr = new XMLHttpRequest;

                                    let t0 = 0;
                                    let d0 = 0;

                                    formData.append("theFile", files[0]);

                                    xhr.onreadystatechange = function() {
                                        //var jsonobj = JSON.parse(this.responseText)
                                        if (this.status == 200 || this.status == 204) {
                                            console.log("this.responseText");
                                            //window.location = "https://uploads.marksism.space" + jsonobj.link;
                                            document.getElementById("done").innerHTML = "<button onclick=\"copyText()\">Copiera länk</button><button onclick=\"location.reload(true);\">Ladda upp en till fil</button>";

                                            document.getElementById("myInput").value = "https://uploads.marksism.space" + this.responseText

                                            document.getElementById("myInput").style.display = "block";

                                        } else {
                                            if(this.status != 0) window.alert("Något gick fel " + this.status)
                                            
                                        }
                                    };

                                    xhr.upload.onprogress = function(e) {
                                        MBps = e.loaded > 0 ?
                                            ((e.loaded - d0) * 0.00000095367432) /
                                            ((performance.now() - t0) / 1000) : 0;
                                        let timeLeft = ((e.total - e.loaded) * 0.00000095367432) / MBps;
                                        timeLeftHumanReadble = timeLeft > 60 ? (timeLeft / 60).toFixed(1) + "m" : timeLeft.toFixed(1) + "s"

                                        //console.log(e.loaded / e.total * 100);
                                        var length = e.loaded / e.total * 100;
                                        var whats_left = 100 - length

                                        //inner_dropzone.style.marginTop = whats_left + "%";

                                        inner_dropzone.style.height = length + "%";

                                        inner_dropzone.style.transition = "all " + e.total / 10000000 + "s";

                                        t0 = performance.now();
                                        d0 = e.loaded;
                                    }

                                    var update_percent = function() {

                                        var style = getComputedStyle(inner_dropzone);

                                        var style2 = getComputedStyle(dropzone);

                                        var height = style.height.slice(0, -2);

                                        var height2 = style2.height.slice(0, -2);

                                        var percent_thing = Math.round(height / height2 * 1000) / 10;

                                        if(percent_thing == 100) {
                                            document.getElementById("text").innerHTML = "Waiting for server"

                                            return false
                                        }

                                        if (percent_thing.toString().indexOf(".") == -1) {
                                            percent_thing = percent_thing + ".0";
                                        }

                                        if ((document.getElementById("done").innerHTML == "")) {
                                            document.getElementById("text").innerHTML = percent_thing + "%<br>(" + parseFloat(MBps).toFixed(1) + " MB/s)<br>" + timeLeftHumanReadble + " kvar";
                                            return true;
                                        } else {
                                            document.getElementById("text").innerHTML = "";
                                            return false;
                                        }

                                    }

                                    xhr.upload.onloadstart = function() {
                                        update_percent();

                                        text.style.fontSize = "xx-large";

                                        dropzone.style.cursor = "default";

                                        function everyFrame() {
                                            if (update_percent()) {
                                                requestAnimationFrame(everyFrame);
                                            }

                                        }

                                        requestAnimationFrame(everyFrame);
                                    }

                                    xhr.open("POST", "https://uploads.marksism.space/");
                                    xhr.send(formData);
                                }

                                var check_file = function(file) {
                                    const images = "image/"

                                    const video = "video/"

                                    if (file[0].size > 300000000) {
                                        alert("Too large (Only images under 300 mb allowed)")
                                        return false;
                                    }
                                    return true;
                                }

                                dropzone.ondrop = function(e) {
                                    e.preventDefault();
                                    this.className = 'dropzone';
                                    if (check_file(e.dataTransfer.files)) {
                                        if (inner_dropzone.style.height < 0.01) {
                                            upload(e.dataTransfer.files);
                                        } else {
                                            alert("Du laddar redan upp en bild")
                                        }

                                    }

                                }

                                dropzone.ondragover = function() {
                                    this.className = 'dropzone dragover';
                                    return false;
                                }

                                dropzone.ondragleave = function() {
                                    this.className = 'dropzone';
                                    return false;
                                }

                                dropzone.onclick = function() {
                                    if (inner_dropzone.style.height < 0.01) {
                                        document.getElementById('file-input').type = 'file';

                                        document.getElementById('file-input').onchange = e => {
                                            var file = e.target.files[0];

                                            if (check_file(e.target.files)) {
                                                upload(e.target.files)
                                            }

                                        }

                                        document.getElementById('file-input').click();
                                    }

                                }
                            }());
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>