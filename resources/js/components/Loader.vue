<template>
    <div class="container">
        <div class="loader"></div>
    </div>
</template>

<script>
export default {
    
}
</script>


<style scoped>

.container {
    display: flex;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999999999999;
    align-items: center;
    justify-content: center;
    background-color: #EEE;
}

.loader {
  display: inline-block;
  width: 42px;
  height: 42px;
}

.loader:after {
  content: " ";
  display: block;
  width: 36px;
  height: 36px;
  margin: 6px;
  border-radius: 50%;
  border: 4px solid crimson;
  border-color: crimson transparent crimson transparent;
  animation: loader-ring 1.2s linear infinite;
}
@keyframes loader-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

</style>
