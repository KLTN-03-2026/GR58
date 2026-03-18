import re

with open('/Users/enma/Downloads/Coding/Case Study_PJ/Petty_VMCS_SaaS/Petty/petty_FE/src/components/trangchu/index.vue', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix hero container
content = content.replace(
    'class="flex gap-16 items-center justify-center px-[120px] py-[100px] w-full"',
    'class="container mx-auto flex flex-col lg:flex-row gap-12 lg:gap-16 items-center justify-between px-6 md:px-12 py-16 md:py-24"'
)
content = content.replace(
    'class="flex flex-col gap-[24px] items-start w-[480px] shrink-0"',
    'class="flex flex-col gap-[24px] items-start w-full lg:w-1/2 max-w-xl shrink-0"'
)
content = content.replace(
    'class="font-extrabold text-[64px] text-[#432323] leading-[72px] tracking-[0px] text-left"',
    'class="font-extrabold text-5xl md:text-6xl lg:text-[64px] text-[#432323] leading-tight tracking-tight text-left"'
)
content = content.replace(
    'class="font-medium text-[#393e46] w-[480px] text-left mt-6"',
    'class="font-medium text-[#393e46] w-full text-left mt-6"'
)
# Fix hero image
content = content.replace(
    'class="h-[480px] w-[700px] rounded-[28.77px] relative shrink-0 overflow-hidden shadow-2xl group"',
    'class="w-full lg:w-1/2 h-[300px] md:h-[480px] rounded-3xl relative shrink-0 overflow-hidden shadow-2xl group"'
)
content = content.replace(
    'class="absolute inset-0 w-full h-[150%] top-[-5.86%] object-cover transition-transform duration-700 group-hover:scale-110"',
    'class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"'
)

# 2. Trust by wrap
content = content.replace(
    'class="flex gap-12 items-center justify-center w-full px-1"',
    'class="flex flex-wrap gap-8 lg:gap-12 items-center justify-center w-full px-4"'
)

# 3. Service cards container & cards
content = content.replace(
    'class="flex gap-6 items-center justify-center w-full"',
    'class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-7xl mx-auto px-6"'
)
content = content.replace(
    'class="relative w-[448px] h-[288px] cursor-pointer group hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden"',
    'class="relative w-full aspect-[4/3] cursor-pointer group hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden"'
)
content = content.replace(
    'w-[380px] shadow-lg"',
    'w-[90%] md:w-[320px] shadow-lg"'
)
content = content.replace(
    'class="text-xs font-medium text-[#393e46] leading-relaxed"',
    'class="text-sm font-medium text-[#393e46] leading-relaxed"'
)

# 4. About Content (fix negative margin and fixed width)
content = content.replace(
    'class="flex items-center justify-center w-full mt-[44px]"',
    'class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center max-w-7xl mx-auto w-full mt-12 px-6"'
)
content = content.replace(
    'class="w-[640px] h-[414px] rounded-[24px] overflow-hidden shadow-2xl -mr-24 z-10 relative"',
    'class="lg:col-span-7 h-[300px] md:h-[414px] w-full rounded-[24px] overflow-hidden shadow-2xl relative lg:-mr-12 z-10"'
)
content = content.replace(
    'class="bg-[#eeeeee] px-[33px] py-6 rounded-2xl shadow-xl w-[576px] h-auto -ml-24 z-20 flex flex-col gap-[10px] justify-center"',
    'class="lg:col-span-5 bg-[#eeeeee] px-6 lg:px-8 py-8 rounded-2xl shadow-xl w-full h-auto z-20 lg:-ml-12 flex flex-col gap-4 justify-center"'
)
content = content.replace(
    'class="flex flex-col gap-8 w-[384px]"',
    'class="flex flex-col gap-8 w-full"'
)

# 5. Stats Section
content = content.replace(
    'class="flex gap-20 justify-center items-center w-full"',
    'class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10 justify-items-center w-full max-w-7xl mx-auto mt-16 px-6 pb-12 cursor-default"'
)
content = content.replace(
     'class="w-px h-[152px] bg-white"',
     'class="hidden lg:block w-px h-32 bg-white/30"'
)
content = content.replace(
     'class="flex gap-9 items-center"',
     'class="flex gap-4 lg:gap-9 items-center w-full"'
)
content = content.replace('w-[184px]', 'w-full')
content = content.replace('w-[228px]', 'w-full')

# 6. Blog alt fix
content = content.replace('<img\n                :src="post.image"\n                alt=""', '<img\n                :src="post.image"\n                :alt="post.title"')
# Fix service alt fix
content = content.replace('alt=""\n              class="absolute inset-0 w-full h-full object-cover"', 'alt="Dịch vụ"\n              class="absolute inset-0 w-full h-full object-cover"')


with open('/Users/enma/Downloads/Coding/Case Study_PJ/Petty_VMCS_SaaS/Petty/petty_FE/src/components/trangchu/index.vue', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fix applied natively!")
