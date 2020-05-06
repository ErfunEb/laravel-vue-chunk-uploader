<template>
    <div>
        <Loader v-if="loading" />
        <table class="table table-striped table-responsive">
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>File Size</th>
                <th>Parts</th>
                <th>Uploaded Parts</th>
                <th>Status</th>
                <th>ReUpload</th>
            </tr>
            <tr v-for="upload in uploads" :key="upload.id">
                <td>{{ upload.id }}</td>
                <td>{{ upload.name }}</td>
                <td>{{ upload.size }} bytes</td>
                <td>{{ upload.chunks + 1 }}</td>
                <td>{{ upload.uploadedChunks }}</td>
                <td>
                    {{ Math.ceil((upload.uploadedChunks / (upload.chunks + 1)) * 100) }} %
                    <br />
                    <Badge v-if="upload.uploaded" text="Uploaded" type="success" />
                    <Badge v-else type="error" text="Failed" />
                </td>
                <td><router-link v-if="upload.uploaded == 0" v-bind:to="'/?fileID=' + upload.uploadedFileID">reupload</router-link></td>
            </tr>
        </table>
    </div>
</template>
<script>

    import Loader from './Loader';
    import Badge from './Badge';

    export default {
        data() {
            return {
                loading: 1,
                uploads: [],
            }
        },
        components: {
            Loader, Badge
        },
        mounted() {

            let myApp = this;

            // Get list of uploads for showing in table
            axios.get('/list').then(function(res) {
                myApp.loading = 0;
                myApp.uploads = res.data.data;
            });
        }
    }

</script>
