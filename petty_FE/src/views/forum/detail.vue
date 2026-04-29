<template>
  <div class="bg-[#F8F9FA] min-h-screen pt-24 pb-16 font-nunito selection:bg-[#5A9690]/20">
    <div class="max-w-4xl mx-auto px-4">
      
      <!-- Skeleton Loading -->
      <div v-if="loading" class="animate-pulse bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
        <div class="h-80 bg-gray-100 rounded-2xl mb-8"></div>
        <div class="h-6 bg-gray-200 rounded-md w-1/4 mb-6"></div>
        <div class="h-12 bg-gray-200 rounded-md w-3/4 mb-6"></div>
        <div class="flex items-center gap-4 mb-10 pb-10 border-b border-gray-100">
          <div class="w-12 h-12 rounded-full bg-gray-200"></div>
          <div class="h-4 bg-gray-200 rounded-md w-32"></div>
        </div>
        <div class="space-y-4">
          <div class="h-4 bg-gray-200 rounded-md w-full"></div>
          <div class="h-4 bg-gray-200 rounded-md w-full"></div>
          <div class="h-4 bg-gray-200 rounded-md w-5/6"></div>
          <div class="h-4 bg-gray-200 rounded-md w-full"></div>
        </div>
      </div>

      <!-- 404 / Error State -->
      <div v-else-if="error || !post" class="bg-white p-16 rounded-3xl text-center shadow-xl shadow-gray-200/40 border border-gray-100">
        <div class="w-24 h-24 bg-red-50 text-red-400 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h2 class="text-3xl font-black text-[#432323] mb-4">Bài viết không tồn tại</h2>
        <p class="text-gray-500 mb-8 text-lg">Xin lỗi, bài viết bạn đang tìm kiếm không có sẵn hoặc đã bị xóa.</p>
        <button @click="router.push('/forum')" class="bg-gradient-to-r from-[#5A9690] to-[#4A807A] text-white font-bold px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-[#5A9690]/30 hover:-translate-y-0.5 transition-all inline-flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Trở về trang chính
        </button>
      </div>

      <!-- Content -->
      <div v-else class="bg-white rounded-3xl shadow-xl shadow-gray-200/30 overflow-hidden border border-gray-100">
        
        <!-- Cover Image Layer -->
        <div v-if="post.anh_bai_viet" class="relative w-full h-[400px] md:h-[500px]">
          <img :src="getImageUrl(post.anh_bai_viet)" alt="Cover" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
          
          <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 text-white">
            <div class="inline-flex items-center bg-white/20 backdrop-blur-md text-white font-bold text-xs px-4 py-1.5 rounded-full mb-4 border border-white/20">
              {{ post.phan_loai_bai_viet?.ten_phan_loai || post.category?.name || 'Danh mục' }}
            </div>
            <h1 class="text-4xl md:text-5xl font-black font-montserrat mb-6 leading-tight text-white drop-shadow-md">
              {{ post.ten_bai_viet || post.tieu_de }}
            </h1>
            
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/50 flex items-center justify-center overflow-hidden shadow-inner">
                <img v-if="post.nguoi_dung?.avatar" :src="getImageUrl(post.nguoi_dung.avatar)" class="w-full h-full object-cover">
                <span v-else class="font-bold text-white text-lg">{{ getInitials(post.nguoi_dung?.name || post.author?.name) }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-bold text-white tracking-wide drop-shadow">{{ post.nguoi_dung?.name || post.author?.name || 'Admin' }}</span>
                <span class="text-sm text-white/80 font-medium">{{ formatDate(post.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- No Cover Header -->
        <div v-else class="p-8 md:p-12 pb-0">
          <div class="inline-flex items-center bg-[#5A9690]/10 text-[#5A9690] font-bold text-xs px-4 py-1.5 rounded-full mb-4">
            {{ post.phan_loai_bai_viet?.ten_phan_loai || post.category?.name || 'Danh mục' }}
          </div>
          <h1 class="text-4xl md:text-5xl font-black font-montserrat mb-8 leading-tight text-[#2C3E50]">
            {{ post.ten_bai_viet || post.tieu_de }}
          </h1>
          
          <div class="flex items-center gap-4 pb-8 border-b border-gray-100">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#5A9690] to-[#82C3BD] flex items-center justify-center overflow-hidden shadow-md text-white">
              <img v-if="post.nguoi_dung?.avatar" :src="getImageUrl(post.nguoi_dung.avatar)" class="w-full h-full object-cover">
              <span v-else class="font-bold text-lg">{{ getInitials(post.nguoi_dung?.name || post.author?.name) }}</span>
            </div>
            <div class="flex flex-col">
              <span class="font-bold text-[#2C3E50]">{{ post.nguoi_dung?.name || post.author?.name || 'Admin' }}</span>
              <span class="text-sm text-gray-400 font-medium">{{ formatDate(post.created_at) }}</span>
            </div>
          </div>
        </div>

        <div class="p-8 md:p-12">
          <!-- HTML Content -->
          <article class="prose prose-lg max-w-none text-[#393E46] mb-16 article-content" v-html="post.noi_dung"></article>

          <!-- Reactions -->
          <div class="flex items-center gap-4 mb-16 py-6 border-y border-gray-100">
            <button 
              @click="handlePostReaction('like')"
              :class="['flex items-center justify-center gap-2.5 px-6 py-3 rounded-2xl border-2 transition-all duration-300 font-bold', hasReacted === 'like' ? 'bg-[#5A9690] text-white border-[#5A9690] shadow-lg shadow-[#5A9690]/30' : 'bg-white text-gray-500 border-gray-200 hover:border-[#5A9690] hover:text-[#5A9690] hover:shadow-md']"
            >
              <svg class="w-6 h-6" :fill="hasReacted === 'like' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
              <span>{{ likesCount }} Thích</span>
            </button>

            <button 
              @click="handlePostReaction('dislike')"
              :class="['flex items-center justify-center gap-2.5 px-6 py-3 rounded-2xl border-2 transition-all duration-300 font-bold', hasReacted === 'dislike' ? 'bg-rose-500 text-white border-rose-500 shadow-lg shadow-rose-500/30' : 'bg-white text-gray-500 border-gray-200 hover:border-rose-500 hover:text-rose-500 hover:shadow-md']"
            >
              <svg class="w-6 h-6" :fill="hasReacted === 'dislike' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
              <span>{{ dislikesCount }} Phản đối</span>
            </button>
          </div>

          <!-- Comments Section -->
          <div id="comments" class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100 shadow-inner">
            <h3 class="text-2xl font-black text-[#2C3E50] mb-8 flex items-center gap-3">
              <svg class="w-7 h-7 text-[#5A9690]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
              Thảo luận ({{ totalComments }})
            </h3>
            
            <!-- Main Comment Form -->
            <div class="mb-10 bg-white p-5 rounded-2xl shadow-sm border border-gray-200/60 focus-within:ring-2 focus-within:ring-[#5A9690]/20 focus-within:border-[#5A9690] transition-all">
              <textarea 
                v-model="newComment" 
                rows="3" 
                class="w-full border-none p-2 focus:outline-none resize-none text-[#393E46] placeholder-gray-400 bg-transparent"
                placeholder="Chia sẻ ý kiến của bạn..."
                :disabled="!isLoggedIn"
              ></textarea>
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-4 pt-4 border-t border-gray-100 gap-4 sm:gap-0">
                <p v-if="!isLoggedIn" class="text-sm text-rose-500 font-medium">
                  Vui lòng <router-link to="/customer/login" class="underline hover:text-rose-700 font-bold">đăng nhập</router-link> để thảo luận.
                </p>
                <p v-else class="text-xs text-gray-400 font-medium">✨ Chia sẻ văn minh, xây dựng cộng đồng Petty.</p>
                
                <button 
                  @click="submitComment(null)"
                  :disabled="!isLoggedIn || submittingComment || !newComment.trim()"
                  class="bg-gradient-to-r from-[#5A9690] to-[#4A807A] text-white font-bold px-8 py-2.5 rounded-xl hover:shadow-lg hover:shadow-[#5A9690]/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none w-full sm:w-auto"
                >
                  {{ submittingComment ? 'Đang gửi...' : 'Gửi bình luận' }}
                </button>
              </div>
            </div>

            <!-- Comments List -->
            <div v-if="commentsLoading" class="flex justify-center py-12">
              <svg class="animate-spin h-10 w-10 text-[#5A9690]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            
            <div v-else-if="rootComments.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-100 border-dashed">
              <div class="w-16 h-16 bg-[#5A9690]/10 text-[#5A9690] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
              </div>
              <p class="text-gray-500 font-medium">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ!</p>
            </div>
            
            <div v-else class="space-y-8">
              <div v-for="comment in rootComments" :key="comment.id" class="flex flex-col gap-4">
                <!-- Root Comment -->
                <div class="flex gap-4 group">
                  <div class="w-12 h-12 shrink-0 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center overflow-hidden shadow-sm">
                    <img v-if="comment.nguoi_dung?.avatar" :src="getImageUrl(comment.nguoi_dung.avatar)" class="w-full h-full object-cover">
                    <span v-else class="font-bold text-gray-500">{{ getInitials(comment.nguoi_dung?.name || comment.author?.name) }}</span>
                  </div>
                  
                  <div class="flex-1 bg-white p-5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100 hover:border-[#5A9690]/30 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                      <div class="flex flex-col">
                        <h4 class="font-bold text-[#2C3E50]">{{ comment.nguoi_dung?.name || comment.author?.name || 'User' }}</h4>
                        <span class="text-xs text-gray-400 font-medium">{{ formatDate(comment.created_at) }}</span>
                      </div>
                      <button 
                        v-if="isOwner(comment.nguoi_dung_id || comment.user_id)" 
                        @click="confirmDeleteComment(comment.id)"
                        class="text-gray-300 hover:text-rose-500 text-sm opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Xóa bình luận"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </div>
                    <p class="text-[#393E46] whitespace-pre-wrap leading-relaxed">{{ comment.noi_dung }}</p>
                    
                    <!-- Comment Actions -->
                    <div class="flex items-center gap-6 mt-4 pt-3 border-t border-gray-50 text-sm font-medium">
                      <button @click="handleCommentReaction(comment, 'like')" :class="['flex items-center gap-1.5 transition-colors', comment.user_reaction === 'like' ? 'text-[#5A9690]' : 'text-gray-400 hover:text-[#5A9690]']">
                        <svg class="w-4 h-4" :fill="comment.user_reaction === 'like' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                        {{ comment.likes_count || 0 }}
                      </button>
                      <button @click="handleCommentReaction(comment, 'dislike')" :class="['flex items-center gap-1.5 transition-colors', comment.user_reaction === 'dislike' ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500']">
                        <svg class="w-4 h-4" :fill="comment.user_reaction === 'dislike' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                        {{ comment.dislikes_count || 0 }}
                      </button>
                      <button @click="toggleReplyForm(comment.id)" class="text-gray-400 hover:text-[#5A9690] ml-2">
                        Trả lời
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Inline Reply Form -->
                <div v-if="replyingTo === comment.id" class="ml-16 bg-white p-4 rounded-2xl shadow-sm border border-[#5A9690]/30 relative before:content-[''] before:absolute before:w-6 before:h-px before:bg-gray-300 before:-left-6 before:top-6">
                  <textarea 
                    v-model="replyContent" 
                    rows="2" 
                    class="w-full border-none p-2 text-sm focus:outline-none resize-none bg-transparent"
                    placeholder="Nhập câu trả lời..."
                    :disabled="!isLoggedIn"
                  ></textarea>
                  <div class="flex justify-end gap-3 mt-3 pt-3 border-t border-gray-100">
                    <button @click="replyingTo = null" class="text-gray-500 hover:text-gray-800 text-sm font-medium px-4 py-2">Hủy</button>
                    <button 
                      @click="submitComment(comment.id)"
                      :disabled="!isLoggedIn || submittingComment || !replyContent.trim()"
                      class="bg-[#2C3E50] text-white text-sm font-bold px-5 py-2 rounded-xl hover:bg-[#1A252F] transition-colors disabled:opacity-50"
                    >
                      {{ submittingComment ? 'Đang gửi...' : 'Gửi trả lời' }}
                    </button>
                  </div>
                </div>

                <!-- Replies -->
                <div v-if="getReplies(comment.id).length > 0" class="ml-10 pl-6 border-l-2 border-gray-200 space-y-5">
                  <div v-for="reply in getReplies(comment.id)" :key="reply.id" class="flex gap-4 group relative">
                    <div class="absolute w-6 h-px bg-gray-200 -left-6 top-5"></div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                      <img v-if="reply.nguoi_dung?.avatar" :src="getImageUrl(reply.nguoi_dung.avatar)" class="w-full h-full object-cover">
                      <span v-else class="font-bold text-gray-500 text-xs">{{ getInitials(reply.nguoi_dung?.name || reply.author?.name) }}</span>
                    </div>
                    
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:border-[#5A9690]/20 transition-colors">
                      <div class="flex justify-between items-start mb-2">
                        <div>
                          <h5 class="font-bold text-[#2C3E50] text-sm">{{ reply.nguoi_dung?.name || reply.author?.name || 'User' }}</h5>
                          <span class="text-[11px] text-gray-400 font-medium">{{ formatDate(reply.created_at) }}</span>
                        </div>
                        <button 
                          v-if="isOwner(reply.nguoi_dung_id || reply.user_id)" 
                          @click="confirmDeleteComment(reply.id)"
                          class="text-gray-300 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity"
                          title="Xóa bình luận"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                      </div>
                      <p class="text-[#393E46] text-sm whitespace-pre-wrap leading-relaxed">{{ reply.noi_dung }}</p>
                      
                      <div class="flex items-center gap-5 mt-3 text-xs font-medium">
                        <button @click="handleCommentReaction(reply, 'like')" :class="['flex items-center gap-1.5 transition-colors', reply.user_reaction === 'like' ? 'text-[#5A9690]' : 'text-gray-400 hover:text-[#5A9690]']">
                          <svg class="w-3.5 h-3.5" :fill="reply.user_reaction === 'like' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                          {{ reply.likes_count || 0 }}
                        </button>
                        <button @click="handleCommentReaction(reply, 'dislike')" :class="['flex items-center gap-1.5 transition-colors', reply.user_reaction === 'dislike' ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500']">
                          <svg class="w-3.5 h-3.5" :fill="reply.user_reaction === 'dislike' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                          {{ reply.dislikes_count || 0 }}
                        </button>
                      </div>
                    </div>
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
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { forumService } from '@/services/forumService';
import { getToken, getUser } from '@/utils/auth';
import { showErrorToast, showSuccessToast } from '@/utils/toast';
import { format } from 'date-fns';

const route = useRoute();
const router = useRouter();

// Post state
const post = ref(null);
const loading = ref(true);
const error = ref(false);

const likesCount = ref(0);
const dislikesCount = ref(0);
const hasReacted = ref(null);

// Comments state
const commentsList = ref([]);
const commentsLoading = ref(false);
const newComment = ref('');
const replyContent = ref('');
const submittingComment = ref(false);
const replyingTo = ref(null);

// Auth state
const isLoggedIn = computed(() => {
  // Check any role token, usually customer for forum
  return !!getToken('customer') || !!getToken('staff') || !!getToken('admin');
});
const currentUser = computed(() => {
  return getUser('customer') || getUser('staff') || getUser('admin');
});

const isOwner = (authorId) => {
  if (!currentUser.value || !authorId) return false;
  return currentUser.value.id === authorId;
};

// Data Fetching
const fetchPost = async () => {
  loading.value = true;
  error.value = false;
  try {
    const id = route.params.id;
    const res = await forumService.getPostById(id);
    post.value = res.data || res;
    
    likesCount.value = post.value.likes_count || 0;
    dislikesCount.value = post.value.dislikes_count || 0;
    hasReacted.value = post.value.user_reaction || null; 
    
    // Fetch comments after post
    fetchComments(id);
  } catch (err) {
    console.error('Error fetching post:', err);
    error.value = true;
    loading.value = false;
  }
};

const fetchComments = async (postId) => {
  commentsLoading.value = true;
  try {
    const res = await forumService.getComments(postId);
    commentsList.value = res.data || res || [];
  } catch (err) {
    console.error('Error fetching comments:', err);
  } finally {
    commentsLoading.value = false;
    loading.value = false;
  }
};

onMounted(() => {
  fetchPost();
});

// Computed structure for comments
const rootComments = computed(() => {
  // If API returns flat array
  return commentsList.value.filter(c => !c.parent_id);
});

const getReplies = (parentId) => {
  // Check if API nested them in `replies`
  const parent = commentsList.value.find(c => c.id === parentId);
  if (parent && parent.replies && Array.isArray(parent.replies)) {
    return parent.replies;
  }
  // Otherwise filter from flat list
  return commentsList.value.filter(c => c.parent_id === parentId);
};

const totalComments = computed(() => {
  let count = 0;
  commentsList.value.forEach(c => {
    count++;
    if (c.replies) count += c.replies.length;
  });
  return count;
});

// Interactions
const checkAuth = () => {
  if (!isLoggedIn.value) {
    showErrorToast("Vui lòng đăng nhập", "Bạn cần đăng nhập để thực hiện chức năng này");
    return false;
  }
  return true;
};

const handlePostReaction = async (type) => {
  if (!checkAuth()) return;

  const previousReaction = hasReacted.value;
  const previousLikes = likesCount.value;
  const previousDislikes = dislikesCount.value;

  // Optimistic update
  if (hasReacted.value === type) {
    if (type === 'like') likesCount.value--;
    if (type === 'dislike') dislikesCount.value--;
    hasReacted.value = null;
  } else {
    if (hasReacted.value === 'like') likesCount.value--;
    if (hasReacted.value === 'dislike') dislikesCount.value--;
    
    if (type === 'like') likesCount.value++;
    if (type === 'dislike') dislikesCount.value++;
    
    hasReacted.value = type;
  }

  try {
    await forumService.toggleReaction({
      reactionable_id: post.value.id,
      reactionable_type: 'bai_viet',
      type: type
    });
  } catch (err) {
    // Rollback
    hasReacted.value = previousReaction;
    likesCount.value = previousLikes;
    dislikesCount.value = previousDislikes;
    showErrorToast("Lỗi", "Không thể tương tác bài viết. Vui lòng thử lại.");
  }
};

const handleCommentReaction = async (comment, type) => {
  if (!checkAuth()) return;

  const previousReaction = comment.user_reaction;
  const previousLikes = comment.likes_count || 0;
  const previousDislikes = comment.dislikes_count || 0;

  // Optimistic update
  if (comment.user_reaction === type) {
    if (type === 'like') comment.likes_count--;
    if (type === 'dislike') comment.dislikes_count--;
    comment.user_reaction = null;
  } else {
    if (comment.user_reaction === 'like') comment.likes_count--;
    if (comment.user_reaction === 'dislike') comment.dislikes_count--;
    
    if (type === 'like') {
      comment.likes_count = (comment.likes_count || 0) + 1;
    }
    if (type === 'dislike') {
      comment.dislikes_count = (comment.dislikes_count || 0) + 1;
    }
    comment.user_reaction = type;
  }

  try {
    await forumService.toggleReaction({
      reactionable_id: comment.id,
      reactionable_type: 'binh_luan',
      type: type
    });
  } catch (err) {
    // Rollback
    comment.user_reaction = previousReaction;
    comment.likes_count = previousLikes;
    comment.dislikes_count = previousDislikes;
    showErrorToast("Lỗi", "Không thể tương tác bình luận.");
  }
};

const toggleReplyForm = (commentId) => {
  if (!checkAuth()) return;
  if (replyingTo.value === commentId) {
    replyingTo.value = null;
    replyContent.value = '';
  } else {
    replyingTo.value = commentId;
    replyContent.value = '';
  }
};

const submitComment = async (parentId = null) => {
  if (!checkAuth()) return;
  
  const content = parentId ? replyContent.value : newComment.value;
  if (!content.trim()) return;

  submittingComment.value = true;
  try {
    const res = await forumService.createComment(post.value.id, {
      noi_dung: content.trim(),
      parent_id: parentId
    });
    
    // Refresh comments or inject newly returned comment
    // Assume API returns the new comment
    if (res && (res.data || res.id)) {
      const created = res.data || res;
      if (parentId) {
        // If it's a flat list, just push
        commentsList.value.push(created);
        replyingTo.value = null;
        replyContent.value = '';
      } else {
        commentsList.value.unshift(created);
        newComment.value = '';
      }
      showSuccessToast("Thành công", "Đã gửi bình luận");
    } else {
      // Fallback: refetch
      await fetchComments(post.value.id);
      replyingTo.value = null;
      replyContent.value = '';
      newComment.value = '';
    }
  } catch (err) {
    showErrorToast("Lỗi", "Không thể gửi bình luận.");
  } finally {
    submittingComment.value = false;
  }
};

const confirmDeleteComment = async (id) => {
  if (!confirm("Bạn có chắc chắn muốn xóa bình luận này?")) return;
  
  try {
    await forumService.deleteComment(id);
    
    // Remove locally
    commentsList.value = commentsList.value.filter(c => c.id !== id);
    // Also check replies array if API nests them
    commentsList.value.forEach(c => {
      if (c.replies) {
        c.replies = c.replies.filter(r => r.id !== id);
      }
    });
    
    showSuccessToast("Thành công", "Đã xóa bình luận");
  } catch (err) {
    showErrorToast("Lỗi", "Không thể xóa bình luận.");
  }
};

// Utils
const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http') || path.startsWith('data:')) return path;
  const baseUrl = import.meta.env.VITE_API_BASE?.replace('/api', '') || 'http://127.0.0.1:8000';
  return `${baseUrl}/storage/${path}`;
};

const getInitials = (name) => {
  if (!name) return 'U';
  return name.charAt(0).toUpperCase();
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};
</script>

<style>
.article-content img {
  max-width: 100%;
  height: auto;
  border-radius: 1rem;
  margin: 2.5rem 0;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
.article-content h1, .article-content h2, .article-content h3, .article-content h4 {
  color: #2C3E50;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
  line-height: 1.3;
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
}
.article-content h2 { font-size: 1.875rem; letter-spacing: -0.025em; }
.article-content h3 { font-size: 1.5rem; }
.article-content p {
  margin-bottom: 1.5rem;
  line-height: 1.8;
  color: #393E46;
  font-size: 1.125rem;
}
.article-content blockquote {
  border-left: 4px solid #5A9690;
  padding-left: 1.5rem;
  font-style: italic;
  color: #5A9690;
  background: #F8F9FA;
  padding: 1.5rem;
  border-radius: 0.5rem;
  margin: 2rem 0;
}
.article-content ul, .article-content ol {
  margin-left: 1.5rem;
  margin-bottom: 1.5rem;
  font-size: 1.125rem;
  color: #393E46;
  line-height: 1.8;
}
.article-content ul { list-style-type: disc; }
.article-content ol { list-style-type: decimal; }
.article-content li { margin-bottom: 0.5rem; }
.article-content a {
  color: #5A9690;
  text-decoration: none;
  font-weight: 600;
}
.article-content a:hover {
  text-decoration: underline;
}
</style>
