<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title> Chunk File Uploader </title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}" />
        <style>

            body {
                margin-top: 60px;
            }

            .hidden {
                display: none;
            }

        </style>
    </head>
    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">File Uploader API</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="{{ url('/') }}">Home
                                <span class="sr-only">(Upload Section)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/uploads') }}">Uploads List</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">

            <div class="row">

                <!-- Blog Entries Column -->
                <div class="col-md-8">

                    <h1 class="my-4">
                        Upload File
                    </h1>


                    <div id="content">
                        <input type="file" id="fileBtn" />
                        <br /><br /><br />
                        <input type="button" onclick="handleUpload()" value="Upload File" />
                        <br /><br />
                        <div class="progress" style="border: 1px solid #007bff;">
                            <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <br />
                        <span id="selectToUploadBadge" class="badge badge-secondary">Select File To Upload</span>
                        <span id="uploadingBadge" class="badge badge-info hidden"></span>
                        <span id="uploadedBadge" class="badge badge-success hidden">Uploaded Successfully</span>
                        <span id="errorBadge" class="badge badge-danger hidden">Error Uploading (Connection Error) </span>
                        <input type="button" id="retryBtn" class="hidden" onclick="handleRetry()" value="Retry" />
                        <span id="timer" class="hidden"></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card my-4">
                        <h5 class="card-header">Simple Upload Rest API</h5>
                        <div class="card-body">
                            Simple view for the rest api, can upload and see list of uploads. with failed and successful uploads.
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Footer -->
        <footer class="py-5 bg-dark" style="margin-top: 50%;">
            <div class="container">
                <p class="m-0 text-center text-white">Copyright &copy; Your Website 2019</p>
            </div>
            <!-- /.container -->
        </footer>




        <script src="{{ asset('js/jQuery.js') }}"></script>
        <script src="{{ asset('js/bootstrap.js') }}"></script>
        <script>

            var request = new XMLHttpRequest();
            var fileID;
            var file;
            var fileBtn = document.getElementById('fileBtn');
            var timer = document.getElementById('timer');

            var uploadedBadge = document.getElementById('uploadedBadge');
            var selectToUploadBadge = document.getElementById('selectToUploadBadge');
            var uploadingBadge = document.getElementById('uploadingBadge');
            var errorBadge = document.getElementById('errorBadge');
            var retryBtn = document.getElementById('retryBtn');

            var progress = document.getElementById('progress');
            var start_time, and_time;


            request.onerror = function() {
                errorBadge.innerHTML = 'Connection Error, cannot connect to server';
                show(errorBadge);
                show(retryBtn);
                hide(uploadingBadge);
            };

            function handleRetry() {

                request.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        var data = JSON.parse(this.responseText);
                        if(data.status) {

                            hide(errorBadge);
                            hide(retryBtn);
                            sendParts(file, data.chunkSize, data.uploadedChunks + 1, data.chunks);

                        } else {
                            errorBadge.innerHTML = data.error;
                        }

                    }
                };

                request.open('GET', 'file/' + fileID, true);
                request.send();

            }

            function randomStr(length) {
                var arr = '123456789abcdefghijklmnopqrstuvwxyz@';
                var ans = '';

                for (var i = length; i > 0; i--) {
                    ans +=
                        arr[Math.floor(Math.random() * arr.length)];
                }

                return ans;
            }

            function handleUpload() {

                fileID = (new Date).getTime() + randomStr(20);

                file = document.getElementById("fileBtn").files[0];
                if(!file) {
                    return;
                }

                var chunkSizeInMB = 1;
                var chunkSize = chunkSizeInMB * 1024 * 1024;
                var chunks = Math.ceil(file.size / chunkSize);
                var chunk = 0;

                start_time = (new Date).getTime();

                sendParts(file, chunkSize, chunk, chunks);
            }

            function show(element) {
                element.style.display = 'inline-block';
            }

            function hide(element) {
                element.style.display = 'none';
            }

            function sendParts(file, chunkSize, chunk, chunks)
            {
                if(chunk <= chunks)
                {
                    var offset = chunk * chunkSize;
                    request.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            chunk++;
                            sendParts(file, chunkSize, chunk, chunks);
                        }
                    };

                    var uploadedPercentage = (chunk / chunks) * 100;

                    show(uploadingBadge);
                    uploadingBadge.innerHTML = 'Uploaded ' + Math.ceil(uploadedPercentage) + '%';

                    if(uploadedPercentage < 100) {
                        show(uploadingBadge);
                        hide(uploadedBadge);
                        hide(selectToUploadBadge);
                    } else {
                        hide(uploadingBadge);
                        show(uploadedBadge);
                        fileBtn.value = '';

                        end_time = (new Date).getTime();
                        timer.innerHTML = (end_time - start_time) + ' ms';
                        show(timer);

                        setTimeout(function() {
                            hide(uploadedBadge);
                            progress.style.width = '0%';
                            show(selectToUploadBadge);
                            hide(timer);
                        }, 8000);

                    }

                    progress.style.width = uploadedPercentage + '%';
                    request.open("POST", "{{ url('/upload') }}", true);
                    request.setRequestHeader('Content-Type', 'application/octet-stream');
                    request.setRequestHeader("X-File-ID", fileID);
                    request.setRequestHeader("X-File-Name", file.name);
                    request.setRequestHeader("X-File-Size", file.size);
                    request.setRequestHeader("X-File-Chunks", chunks);
                    request.setRequestHeader("X-File-Chunk-Size", chunkSize);
                    request.setRequestHeader("X-File-Current-Chunk", chunk);
                    request.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    request.setRequestHeader("X-File-Type", file.type);
                    request.send(file.slice(offset, offset + chunkSize));


                }
                }
            }

        </script>
    </body>
</html>
