<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("../Location: login.php");
    exit();
}

//Fetch Username
$query_username = "SELECT username FROM users WHERE role = 'User'";
$result_username = $conn->query($query_username);
$username = $result_username->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Unity WebGL Player | Caps</title>
    <link rel="shortcut icon" href="TemplateData/favicon.ico">
    <link rel="stylesheet" href="TemplateData/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url("../Background/bg.jpg") no-repeat center/cover fixed;
            overflow-x: hidden;
        }
    </style>
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" style="background: url('../Background/bg.jpg') no-repeat center/cover fixed;">
        <div class="container-fluid">
          <div class="d-flex flex-column">
                <span class="badge bg-info text-white px-3 mb-1">WELCOME TO</span>
                    <h4 class="text-white mb-0 fw-bolder text-uppercase border-bottom border-info pb-1">VR EMOTIONAL WELL-BEING</h4>
                        <span class="badge bg-light text-primary mt-1">
                            <?php echo strtoupper($username['username']); ?>
                            <i class="bi bi-person-check-fill ms-2"></i>
                        </span>
                    </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="../index.php" class="nav-link fw-bold text-light d-flex align-items-center">Home</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="../progress.php" class="nav-link fw-bold text-light d-flex align-items-center">View Progress</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="../VR/index.php" class="nav-link fw-bold text-light d-flex align-items-center">Start VR</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="../quiz.php" class="nav-link fw-bold text-light d-flex align-items-center">Take Quiz</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="../logout.php" class="nav-link fw-bold text-light d-flex align-items-center">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="unity-container" class="unity-desktop">
      <canvas id="unity-canvas" width=960 height=600></canvas>
      <div id="unity-loading-bar">
        <div id="unity-logo"></div>
        <div id="unity-progress-bar-empty">
          <div id="unity-progress-bar-full"></div>
        </div>
      </div>
      <div id="unity-warning"> </div>
      <div id="unity-footer">
        <div id="unity-webgl-logo"></div>
        <div id="unity-fullscreen-button"></div>
        <div id="unity-build-title">Caps</div>
      </div>
    </div>
    <script>
      var container = document.querySelector("#unity-container");
      var canvas = document.querySelector("#unity-canvas");
      var loadingBar = document.querySelector("#unity-loading-bar");
      var progressBarFull = document.querySelector("#unity-progress-bar-full");
      var fullscreenButton = document.querySelector("#unity-fullscreen-button");
      var warningBanner = document.querySelector("#unity-warning");

      // Shows a temporary message banner/ribbon for a few seconds, or
      // a permanent error message on top of the canvas if type=='error'.
      // If type=='warning', a yellow highlight color is used.
      // Modify or remove this function to customize the visually presented
      // way that non-critical warnings and error messages are presented to the
      // user.
      function unityShowBanner(msg, type) {
        function updateBannerVisibility() {
          warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
        }
        var div = document.createElement('div');
        div.innerHTML = msg;
        warningBanner.appendChild(div);
        if (type == 'error') div.style = 'background: red; padding: 10px;';
        else {
          if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
          setTimeout(function() {
            warningBanner.removeChild(div);
            updateBannerVisibility();
          }, 5000);
        }
        updateBannerVisibility();
      }

      var buildUrl = "Build";
      var loaderUrl = buildUrl + "/VR.loader.js";
      var config = {
        dataUrl: buildUrl + "/VR.data.unityweb",
        frameworkUrl: buildUrl + "/VR.framework.js.unityweb",
        codeUrl: buildUrl + "/VR.wasm.unityweb",
        streamingAssetsUrl: "StreamingAssets",
        companyName: "DefaultCompany",
        productName: "Caps",
        productVersion: "0.1",
        showBanner: unityShowBanner,
      };

      // By default, Unity keeps WebGL canvas render target size matched with
      // the DOM size of the canvas element (scaled by window.devicePixelRatio)
      // Set this to false if you want to decouple this synchronization from
      // happening inside the engine, and you would instead like to size up
      // the canvas DOM size and WebGL render target sizes yourself.
      // config.matchWebGLToCanvasSize = false;

      // If you would like all file writes inside Unity Application.persistentDataPath
      // directory to automatically persist so that the contents are remembered when
      // the user revisits the site the next time, uncomment the following line:
      // config.autoSyncPersistentDataPath = true;
      // This autosyncing is currently not the default behavior to avoid regressing
      // existing user projects that might rely on the earlier manual
      // JS_FileSystem_Sync() behavior, but in future Unity version, this will be
      // expected to change.

      if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
        // Mobile device style: fill the whole browser client area with the game canvas:

        var meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, shrink-to-fit=yes';
        document.getElementsByTagName('head')[0].appendChild(meta);
        container.className = "unity-mobile";
        canvas.className = "unity-mobile";

        // To lower canvas resolution on mobile devices to gain some
        // performance, uncomment the following line:
        // config.devicePixelRatio = 1;

        unityShowBanner('WebGL builds are not supported on mobile devices.');
      } else {
        // Desktop style: Render the game canvas in a window that can be maximized to fullscreen:

        canvas.style.width = "960px";
        canvas.style.height = "600px";
      }

      loadingBar.style.display = "block";

      var script = document.createElement("script");
      script.src = loaderUrl;
      script.onload = () => {
        createUnityInstance(canvas, config, (progress) => {
          progressBarFull.style.width = 100 * progress + "%";
        }).then((unityInstance) => {
          loadingBar.style.display = "none";
          fullscreenButton.onclick = () => {
            unityInstance.SetFullscreen(1);
          };
        }).catch((message) => {
          alert(message);
        });
      };
      document.body.appendChild(script);
    </script>
  </body>
</html>
