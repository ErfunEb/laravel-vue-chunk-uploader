<template>
    <div>
        <Navbar />
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <router-view></router-view>
                </div>
                <SideWidget></SideWidget>
            </div>

        </div>
        <Footer />
    </div>
</template>
<script>

    import Navbar from "./Navbar";
    import Footer from "./Footer";
    import SideWidget from "./SideWidget";

    export default {
        components: {
            Navbar,
            Footer,
            SideWidget
        }
    }
</script>
