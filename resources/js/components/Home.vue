<template>
    <div id="content">
        <h1 class="my-4">
            Upload File
        </h1>
        <input type="file" id="fileBtn" ref="fileInput" @change="getFile" />
        <br /><br /><br />
        <input v-if="ShowUploadBtn" type="button" @click="handleUpload()" value="Upload File" />
        <br /><br />
        <ProgressBar v-bind:percentage="this.uploadedPercentage" />
        <br />
        <Badge v-if="SelectToUploadBadge" type="secondary" text="Select File To Upload" />
        <Badge v-if="ProgressBadge" type="progress" v-bind:text="'Uploading ' + Math.ceil(this.uploadedPercentage) + '%'" />
        <Badge v-if="UploadedBadge" type="success" text="Uploaded Successfully" />
        <Badge v-if="ErrorBadge" type="error" v-bind:text="ErrorBadgeText" />
        <input type="button" v-if="retryBtn" @click="handleError()" value="Retry" />
        <span v-if="ShowTimer">{{ time + ' ms' }}</span>
    </div>
</template>

<script>

    import ProgressBar from './ProgressBar';
    import Badge from './Badge';

    export default {
        components: {
            ProgressBar, Badge
        },
        data() {
            return {
                file: null,
                fileID: null,
                chunkSizeInMB: 20,
                chunkSize: null,
                chunk: null,
                chunks: null,
                start_time: null,
                end_time: null,
                uploadedPercentage: null,
                SelectToUploadBadge: 1,
                ProgressBadge: 0,
                UploadedBadge: 0,
                ErrorBadge: 0,
                ShowTimer: 0,
                time: null,
                ErrorBadgeText: '',
                retryBtn: 0,
                ShowUploadBtn: 1
            }
        },
        methods: {
            randomStr: (length) => {

                // Generating random string for fileID

                var arr = '123456789abcdefghijklmnopqrstuvwxyz@';
                var ans = '';

                for (var i = length; i > 0; i--) {
                    ans +=
                        arr[Math.floor(Math.random() * arr.length)];
                }

                return ans;
            },
            getFile(event) {
                // stores the file in data after selecting the file by input
                this.file = event.target.files[0];
            },
            handleUpload() {
                this.ShowUploadBtn = 0;

                // looks for query string for a re upload request
                if(this.$route.query.fileID) {

                    this.fileID = this.$route.query.fileID;
                    // calls the handleError method for fetching the uploaded file info and continuing the upload
                    this.handleError();
                    return;

                }

                // generating random fileID and calculating chunks
                this.fileID = (new Date).getTime() + this.randomStr(20);

                if(!this.file) {
                    return;
                }

                this.chunkSize = this.chunkSizeInMB * 1024 * 1024;
                this.chunks = Math.ceil(this.file.size / this.chunkSize);
                this.chunk = 0;

                // start time for calculating
                this.start_time = (new Date).getTime();

                // sends the parts until it finishes
                this.sendParts();
            },
            sendParts() {

                // offset file file slicing and partitioning
                let offset = this.chunk * this.chunkSize;
                let myApp = this;

                // sending the file part
                axios({
                    url: '/upload',
                    method: 'POST',
                    data: this.file.slice(offset, offset + this.chunkSize),
                    headers: {
                        'Content-Type': 'application/octet-stream',
                        "X-File-ID": this.fileID,
                        "X-File-Name": this.file.name,
                        'X-File-Size': this.file.size,
                        "X-File-Chunks": this.chunks,
                        "X-File-Chunk-Size": this.chunkSize,
                        "X-File-Current-Chunk": this.chunk,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "X-File-Type": this.file.type
                    }
                }).then(function(res) {

                    if(res.data.uploaded == 1) {

                        // UI controls, badges, texts, and progress bar
                        myApp.end_time = (new Date).getTime();
                        myApp.time = myApp.end_time - myApp.start_time;
                        myApp.ShowTimer = 1;
                        myApp.ProgressBadge = 0;
                        myApp.UploadedBadge = 1;


                        // reseting the file input and querystring for the new file upload
                        myApp.fileID = null;
                        myApp.$refs.fileInput.value = '';
                        myApp.$router.push('/');


                        setTimeout(function() {
                            myApp.UploadedBadge = 0;
                            myApp.uploadedPercentage = 0;
                            myApp.SelectToUploadBadge = 1;
                            myApp.ShowTimer = 0;
                            myApp.ShowUploadBtn = 1;
                        }, 8000);

                    } else {

                        // Not finished, slices the next parts

                        myApp.chunk++;
                        myApp.ProgressBadge = 1;
                        myApp.SelectToUploadBadge = 0;
                        myApp.sendParts();
                    }

                }).catch(function() {

                    // in case of lost connection to the server, error handling
                    myApp.ErrorBadgeText = 'Connection Error, cannot connect to server';
                    myApp.ErrorBadge = 1;
                    myApp.retryBtn = 1;
                    myApp.ProgressBadge = 0;

                });

                // calculating uploaded percentage based of chunks count and chunks uploaded
                this.uploadedPercentage = (this.chunk / (this.chunks + 1)) * 100;

            },
            handleError() {

                // error handling for failed uploads or lost connection.

                let myApp = this;

                // get upload status and information
                axios.get('file/' + this.fileID).then(function(res) {
                    if(res.data.status) {

                        // After retriving how many parts uploaded and how many left, continues the upload
                        myApp.ErrorBadge = 0;
                        myApp.retryBtn = 0;
                        myApp.chunkSize = res.data.chunkSize + 1;
                        myApp.chunk = res.data.uploadedChunks;
                        myApp.chunks = res.data.chunks;
                        myApp.sendParts();
                        myApp.retryBtn = 0;

                    } else {

                        // Showing error in case of a wrong fileID or uploaded file not found at all
                        myApp.ErrorBadgeText = res.data.error;
                        myApp.ErrorBadge = 1;
                    }
                });

            }
        }
    }
</script>
