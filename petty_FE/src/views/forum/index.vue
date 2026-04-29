<template>
  <div class="bg-[#F8F9FA] min-h-screen pt-24 pb-16 font-nunito">
    <!-- Hero Banner -->
    <div class="max-w-7xl mx-auto px-4 mb-10">
      <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#5A9690] to-[#3B6B66] p-10 md:p-14 text-white shadow-xl shadow-[#5A9690]/20">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-56 h-56 rounded-full bg-black opacity-15 blur-2xl"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 rounded-full bg-[#82C3BD] opacity-20 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
          <div class="max-w-xl">
            <h1 class="text-4xl md:text-5xl font-black font-montserrat mb-4 tracking-tight leading-tight">
              Cộng đồng <span class="text-[#A7F3D0]">Petty</span>
            </h1>
            <p class="text-white/85 text-lg md:text-xl font-medium max-w-lg">
              Nơi chia sẻ kiến thức, kinh nghiệm và tình yêu vô bờ bến dành cho thú cưng của bạn.
            </p>
          </div>
          
          <div class="w-full md:w-[400px] relative group">
            <div class="absolute -inset-1 bg-white/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
            <div class="relative flex items-center bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-1.5 shadow-inner transition-all duration-300 focus-within:bg-white/20 focus-within:border-white/40">
              <svg class="w-6 h-6 text-white/80 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Tìm bài viết, cẩm nang..." 
                class="w-full bg-transparent border-none text-white placeholder-white/70 focus:ring-0 px-4 py-3 outline-none font-medium"
              >
              <button v-if="searchQuery" @click="searchQuery = ''" class="mr-3 text-white/60 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 flex flex-col lg:flex-row gap-8">
      
      <!-- Sidebar Categories (Sticky) -->
      <div class="w-full lg:w-72 shrink-0">
        <div class="sticky top-28 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-black text-[#432323] text-xl mb-6 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#5A9690]/10 flex items-center justify-center text-[#5A9690]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </div>
            Danh mục
          </h2>
          <ul class="space-y-2">
            <li>
              <button 
                @click="selectedCategoryId = null"
                :class="['w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 font-semibold', selectedCategoryId === null ? 'bg-[#5A9690] text-white shadow-md shadow-[#5A9690]/30 translate-x-1' : 'text-[#393E46] hover:bg-gray-50 hover:text-[#5A9690] hover:translate-x-1']"
              >
                <span>Tất cả bài viết</span>
                <span v-if="selectedCategoryId === null" class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
              </button>
            </li>
            <li v-for="category in categories" :key="category.id">
              <button 
                @click="selectedCategoryId = category.id"
                :class="['w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 font-semibold', selectedCategoryId === category.id ? 'bg-[#5A9690] text-white shadow-md shadow-[#5A9690]/30 translate-x-1' : 'text-[#393E46] hover:bg-gray-50 hover:text-[#5A9690] hover:translate-x-1']"
              >
                <span>{{ category.ten_phan_loai || category.name || 'Category' }}</span>
                <span v-if="selectedCategoryId === category.id" class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
              </button>
            </li>
          </ul>
          
          <!-- Decorative bottom banner -->
          <div class="mt-8 rounded-xl bg-amber-50 p-5 border border-amber-100 relative overflow-hidden">
            <svg class="absolute -bottom-4 -right-4 w-24 h-24 text-amber-200/50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            <h3 class="font-bold text-amber-900 mb-1 relative z-10">Góc chia sẻ</h3>
            <p class="text-sm text-amber-800/80 relative z-10 leading-relaxed">Tham gia cùng cộng đồng Petty để chăm sóc thú cưng tốt hơn mỗi ngày.</p>
          </div>
        </div>
      </div>

      <!-- Post List Area -->
      <div class="flex-1">
        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 animate-pulse">
            <div class="h-48 bg-gray-200 rounded-xl mb-5"></div>
            <div class="h-6 bg-gray-200 rounded-md w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded-md w-1/4 mb-5"></div>
            <div class="h-16 bg-gray-200 rounded-md w-full mb-5"></div>
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                <div class="h-4 bg-gray-200 rounded-md w-20"></div>
              </div>
              <div class="h-4 bg-gray-200 rounded-md w-16"></div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-100 p-10 rounded-2xl text-center shadow-sm">
          <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <h3 class="text-xl font-bold text-red-800 mb-2">Đã xảy ra lỗi</h3>
          <p class="text-red-600 mb-6">Không thể tải dữ liệu diễn đàn lúc này. Vui lòng thử lại sau.</p>
          <button @click="fetchData" class="bg-red-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">
            Thử lại
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredPosts.length === 0" class="bg-white border border-gray-100 p-16 rounded-2xl text-center shadow-sm">
          <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          <h3 class="text-xl font-bold text-[#432323] mb-2">
            {{ searchQuery ? 'Không tìm thấy kết quả' : 'Chưa có bài viết nào' }}
          </h3>
          <p class="text-gray-500 max-w-md mx-auto">
            {{ searchQuery ? `Không có bài viết nào khớp với từ khóa "${searchQuery}". Hãy thử dùng từ khóa khác.` : 'Danh mục này hiện chưa có bài viết nào được xuất bản.' }}
          </p>
          <button v-if="searchQuery" @click="searchQuery = ''" class="mt-6 text-[#5A9690] font-bold hover:underline">
            Xóa tìm kiếm
          </button>
        </div>

        <!-- Posts Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div 
            v-for="post in filteredPosts" 
            :key="post.id"
            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 cursor-pointer flex flex-col overflow-hidden hover:-translate-y-1"
            @click="goToDetail(post.id)"
          >
            <!-- Image Container -->
            <div class="h-52 relative overflow-hidden bg-gray-100">
              <img 
                v-if="post.anh_bai_viet" 
                :src="getImageUrl(post.anh_bai_viet)" 
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                alt="Post cover"
              >
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              
              <!-- Gradient Overlay for contrast -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
              
              <!-- Floating Badge -->
              <div class="absolute top-4 left-4">
                <span class="bg-white/90 backdrop-blur-sm text-[#5A9690] text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">
                  {{ getCategoryName(post.phan_loai_id || post.phan_loai_bai_viet_id) }}
                </span>
              </div>
            </div>
            
            <!-- Content -->
            <div class="p-5 flex flex-col flex-1">
              <h3 class="font-bold text-xl text-[#432323] mb-3 line-clamp-2 leading-tight group-hover:text-[#5A9690] transition-colors">
                {{ post.ten_bai_viet || post.tieu_de }}
              </h3>
              <p class="text-[#393E46] text-sm mb-5 line-clamp-3 flex-1 leading-relaxed opacity-80">
                {{ post.mo_ta }}
              </p>
              
              <!-- Footer Meta -->
              <div class="pt-4 mt-auto border-t border-gray-50 flex items-center justify-between">
                <!-- Author -->
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#5A9690] to-[#82C3BD] flex items-center justify-center overflow-hidden shadow-sm text-white">
                    <img v-if="post.nguoi_dung?.avatar" :src="getImageUrl(post.nguoi_dung.avatar)" class="w-full h-full object-cover">
                    <span v-else class="font-bold text-sm">{{ getInitials(post.nguoi_dung?.name || post.author?.name || 'A') }}</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-[#432323] leading-none">{{ post.nguoi_dung?.name || post.author?.name || 'Admin' }}</span>
                    <span class="text-[11px] text-gray-400 font-medium mt-1">{{ formatDate(post.created_at) }}</span>
                  </div>
                </div>
                
                <!-- Stats -->
                <div class="flex items-center gap-3 text-gray-400">
                  <div class="flex items-center gap-1.5 group-hover:text-[#5A9690] transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                    <span class="text-xs font-bold">{{ post.likes_count || 0 }}</span>
                  </div>
                  <div class="flex items-center gap-1.5 group-hover:text-[#5A9690] transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-xs font-bold">{{ post.comments_count || 0 }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { forumService } from '@/services/forumService';
import { format } from 'date-fns';

const router = useRouter();

const posts = ref([]);
const categories = ref([]);
const loading = ref(true);
const error = ref(false);

const searchQuery = ref('');
const selectedCategoryId = ref(null);

const fetchData = async () => {
  loading.value = true;
  error.value = false;
  try {
    const [postsRes, categoriesRes] = await Promise.all([
      forumService.getPosts(),
      forumService.getCategories()
    ]);
    
    // Extract data handling different structures
    let rawPosts = Array.isArray(postsRes) ? postsRes : (postsRes.data || []);
    let rawCategories = Array.isArray(categoriesRes) ? categoriesRes : (categoriesRes.data || []);
    
    // Filter only published if backend doesn't do it
    posts.value = rawPosts.filter(p => p.trang_thai === 'published' || p.trang_thai === 1 || !p.trang_thai);
    categories.value = rawCategories;
  } catch (err) {
    console.error('Error fetching forum data:', err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchData();
});

const removeAccents = (str) => {
  if (!str) return '';
  return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
};

const filteredPosts = computed(() => {
  return posts.value.filter(post => {
    // Filter by category
    const matchCategory = selectedCategoryId.value === null || post.phan_loai_id === selectedCategoryId.value || post.phan_loai_bai_viet_id === selectedCategoryId.value;
    
    // Filter by search query (title)
    const query = removeAccents(searchQuery.value);
    const title = removeAccents(post.ten_bai_viet || post.tieu_de || '');
    const matchSearch = query === '' || title.includes(query);
    
    return matchCategory && matchSearch;
  });
});

const getCategoryName = (id) => {
  if (!id) return 'Khác';
  const cat = categories.value.find(c => c.id === id);
  return cat ? (cat.ten_phan_loai || cat.name) : 'Khác';
};

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http') || path.startsWith('data:')) return path;
  const baseUrl = import.meta.env.VITE_API_BASE?.replace('/api', '') || 'http://127.0.0.1:8000';
  return `${baseUrl}/storage/${path}`;
};

const getInitials = (name) => {
  if (!name) return 'A';
  return name.charAt(0).toUpperCase();
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'dd/MM/yyyy');
};

const goToDetail = (id) => {
  router.push(`/forum/${id}`);
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
