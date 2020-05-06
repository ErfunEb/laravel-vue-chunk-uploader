<template>
    <div class="progress" style="border: 1px solid #007bff;">
        <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" v-bind:style="'width: ' + this.percentage + '%'"></div>
    </div>
</template>
<script>
    export default {
        props: ['percentage']
    }
</script>
