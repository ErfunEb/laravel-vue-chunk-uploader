<template>
    <div>
        <span v-if="this.type == 'secondary'" class="badge badge-secondary">{{ text }}</span>
        <span v-if="this.type == 'success'" class="badge badge-success">{{ text }}</span>
        <span v-if="this.type == 'error'" class="badge badge-danger">{{ text }}</span>
        <span v-if="this.type == 'progress'" class="badge badge-info">{{ text }}</span>
    </div>
</template>
<script>
    export default {
        props: ['type', 'text']
    }
</script>
