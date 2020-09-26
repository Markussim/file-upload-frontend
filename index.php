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
                            <p id="text">Släpp eller tryck här för att ladda upp</p>
                            <input id="file-input" type="file" name="name" style="display: none;" accept="image/*,video/*" />
                        </div>
                        <style>
                            .dropzone {
                                position: relative;
                                display: flex;
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
                            (function() {
                                var dropzone = document.getElementById('dropzone');

                                var inner_dropzone = document.getElementById('inner_dropzone');

                                var text = document.getElementById('text');

                                var upload = function(files) {
                                    var formData = new FormData();
                                    var xhr = new XMLHttpRequest;

                                    formData.append("file", files[0]);

                                    xhr.onreadystatechange = function() {
                                        var jsonobj = JSON.parse(this.responseText)
                                        if (this.readyState == 4 && this.status == 200) {
                                            console.log(this.responseText);
                                            window.location = jsonobj.linkToFile;
                                        } else {
                                            if (jsonobj.error === "Only images allowed") {
                                                alert("Endast bilder och videos är tillåtna");
                                            }
                                            if (jsonobj.error === "Max file size has been exeeded") {
                                                alert("Din fil var för stor");
                                            }
                                        }
                                    };

                                    xhr.upload.onprogress = function(e) {
                                        //console.log(e.loaded / e.total * 100);
                                        var length = e.loaded / e.total * 100;
                                        var whats_left = 100 - length

                                        //inner_dropzone.style.marginTop = whats_left + "%";

                                        inner_dropzone.style.height = length + "%";

                                        inner_dropzone.style.transition = "all " + e.total / 10000000 + "s";
                                    }

                                    var update_percent = function() {

                                        var style = getComputedStyle(inner_dropzone);

                                        var style2 = getComputedStyle(dropzone);

                                        var height = style.height.slice(0, -2);

                                        var height2 = style2.height.slice(0, -2);

                                        var percent_thing = Math.round(height / height2 * 1000) / 10;

                                        if (percent_thing.toString().indexOf(".") == -1) {
                                            percent_thing = percent_thing + ".0";
                                        }

                                        document.getElementById("text").innerHTML = percent_thing + "%";

                                        if (percent_thing == 100) {
                                            document.getElementById("text").innerHTML = "Väntar på servern...";
                                        }

                                    }

                                    xhr.upload.onloadstart = function() {
                                        update_percent();

                                        text.style.fontSize = "xx-large";

                                        dropzone.style.cursor = "default";

                                        function everyFrame() {
                                            update_percent();
                                            requestAnimationFrame(everyFrame);
                                        }

                                        requestAnimationFrame(everyFrame);
                                    }

                                    xhr.open("POST", "https://uploads.marksism.space/");
                                    xhr.send(formData);
                                }

                                var check_file = function(file) {
                                    const images = "image/"

                                    const video = "video/"

                                    if (file[0].size > 25000000) {
                                        if (!((file[0].type.includes(images)) || (file[0].type.includes(video)))) {
                                            alert("Din fil var för stor och fel filformat (Bara bilder under 25 megabyte är tillåtna)");
                                            return false;
                                        }
                                        alert("Too large (Only images under 25 mb allowed)")
                                        return false;
                                    }

                                    if (!((file[0].type.includes(images)) || (file[0].type.includes(video)))) {
                                        alert("Fel fil filformat (Bara bilder under 25 megabyte är tillåtna)");
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

                                            upload(e.target.files)
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