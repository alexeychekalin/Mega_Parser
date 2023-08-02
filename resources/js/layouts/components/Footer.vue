<template>
  <div class="h-100 d-flex align-center justify-space-between">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center">
      &copy;
      {{ new Date().getFullYear() }}
      сделано с
      <VIcon
        icon="mdi-heart-outline"
        color="error"
        size="1.25rem"
        class="mx-1"
      />
      командой <a
        href="https://team-pack.ru"
        target="_blank"
        rel="noopener noreferrer"
        class="text-primary ms-1"
      >Team Pack</a>
    </span>
  </div>
</template>
