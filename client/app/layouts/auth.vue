<template>
  <div class="relative min-h-screen w-screen flex flex-col items-center justify-center bg-zinc-950 text-zinc-100 overflow-hidden font-sans select-none">
    <!-- Immersive glowing gradient circles behind the content -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-orange-600/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-radial from-orange-500/5 to-transparent blur-3xl pointer-events-none"></div>

    <!-- Background Grid Effect -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] pointer-events-none"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-md p-6 flex flex-col gap-6 items-center">
      <!-- Logo and App Name -->
      <div class="flex flex-col items-center gap-2 mb-2 animate-fade-in">
        <div class="flex items-center justify-center p-3 rounded-2xl bg-zinc-900 border border-zinc-800 shadow-xl ring-1 ring-white/5">
          <img
            src="/image.png"
            alt="Vendor Bridge Logo"
            class="h-10 w-auto object-contain"
            @error="onImgError"
          />
        </div>
        <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-orange-400 to-amber-500 bg-clip-text text-transparent">
          Vendor Bridge
        </h1>
        <p class="text-xs text-zinc-400 text-center max-w-[280px]">
          Procurement and supplier coordination platform
        </p>
      </div>

      <!-- Auth Form Card Slot -->
      <div class="w-full bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl shadow-2xl p-1 relative ring-1 ring-white/5">
        <slot />
      </div>
      
      <!-- Footer details -->
      <div class="text-xs text-zinc-500 text-center mt-2">
        &copy; 2026 Vendor Bridge. All rights reserved.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Simple image fallback in case image.png is not found in public/
function onImgError(e: Event) {
  const target = e.target as HTMLImageElement;
  target.src = 'https://img.icons8.com/color/96/bridge.png'; // fallback icon
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
