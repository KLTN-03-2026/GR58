<template>
    <!-- đây là nơi nối Header + Component + Footer lại với nhau -->
    <!-- Header -->
    <div class="wrapper">
		<div class="header-wrapper">
            <TopTrangChu></TopTrangChu>
		</div>
        <!-- Body Contain -->
		<div class="page-wrapper">
			<div class="page-content">
                <router-view> </router-view>
            </div>
        <!-- Footer -->
		</div>
        <BotTrangChu></BotTrangChu>
	</div>
</template>
<script>
import TopTrangChu from "../components/TopTrangChu.vue";
import BotTrangChu from "../components/BotTrangChu.vue";
import "../../assets/js/bootstrap.bundle.min.js";
import "../../assets/js/jquery.min.js";
import "../../assets/plugins/simplebar/js/simplebar.min.js";
import "../../assets/plugins/metismenu/js/metisMenu.min.js";
import "../../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js";
import "../../assets/js/index.js";
import "../../assets/js/app.js";
import "../../assets/js/pace.min.js";
export default {
    name        :   "app",
    components  :   {
       TopTrangChu, BotTrangChu
    }
}
</script>
<style>
@import "../../assets/plugins/simplebar/css/simplebar.css";
@import "../../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css";
@import "../../assets/plugins/metismenu/css/metisMenu.min.css";
@import "../../assets/css/pace.min.css";
@import "../../assets/css/bootstrap.min.css";
@import "../../assets/css/bootstrap-extended.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import "../../assets/css/app.css";
@import "../../assets/css/icons.css";
@import "../../assets/css/dark-theme.css";
@import "../../assets/css/semi-dark.css";
@import "../../assets/css/header-colors.css";
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
</style>

<style scoped>
/* Reset paddings for full-width layout, overriding app.css defaults */
.page-wrapper {
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
    margin-top: 60px !important;
}

.page-content {
    padding: 0 !important;
    width: 100%;
}
</style>