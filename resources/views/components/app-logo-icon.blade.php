{{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" {{ $attributes }}>
    <path 
        fill="currentColor" 
        fill-rule="evenodd" 
        clip-rule="evenodd"
        d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 16.2-9v-8.442l7.6-4.223V9.856l-8.6-4.777-8.6 4.777V18.3l-5.6 3.111V5.633ZM38 18.301l-5.6 3.11v-6.157l5.6-3.11V18.3Zm-1.06-7.856-5.54 3.078-5.54-3.079 5.54-3.078 5.54 3.079ZM24.8 18.3v-6.157l5.6 3.111v6.158L24.8 18.3Zm-1 1.732 5.54 3.078-13.14 7.302-5.54-3.078 13.14-7.3v-.002Zm-16.2 7.89 7.6 4.222V38.3L2 30.966V7.92l5.6 3.111v16.892ZM8.6 9.3 3.06 6.222 8.6 3.143l5.54 3.08L8.6 9.3Zm21.8 15.51-13.2 7.334V38.3l13.2-7.334v-6.156ZM9.6 11.034l5.6-3.11v14.6l-5.6 3.11v-14.6Z"
    />
</svg> --}}


<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="256" height="256" aria-labelledby="title">
  <title id="title">Wallet with cash icon</title>

  <!-- Background circle with radial/linear blend -->
  <defs>
    <radialGradient id="bgGrad" cx="50%" cy="35%" r="70%">
      <stop offset="0%" stop-color="#6fe0ff"/>
      <stop offset="55%" stop-color="#3fb3ff"/>
      <stop offset="100%" stop-color="#2a9bff"/>
    </radialGradient>

    <!-- Wallet gradient -->
    <linearGradient id="walletGrad" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0" stop-color="#0f6070"/>
      <stop offset="1" stop-color="#0b4750"/>
    </linearGradient>

    <!-- Bill gradient -->
    <linearGradient id="billGrad" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0" stop-color="#a6f57a"/>
      <stop offset="1" stop-color="#6ed05c"/>
    </linearGradient>

    <filter id="softShadow" x="-50%" y="-50%" width="200%" height="200%">
      <feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="#000" flood-opacity="0.12"/>
    </filter>
  </defs>

  <!-- Background circle -->
  <g filter="url(#softShadow)">
    <circle cx="128" cy="128" r="118" fill="url(#bgGrad)"/>
  </g>

  <!-- Slight glossy highlight on circle -->
  <path d="M22 86c30-40 86-66 156-66 6 0 12 0 18 1 -52 6-97 34-124 73C64 97 43 84 22 86z"
        fill="#ffffff" opacity="0.06"/>

  <!-- Bills (behind wallet) -->
  <g transform="translate(36,38) rotate(-8 80 64)">
    <rect x="80" y="40" width="120" height="66" rx="6" ry="6" fill="url(#billGrad)"/>
    <rect x="68" y="28" width="120" height="66" rx="6" ry="6" fill="url(#billGrad)" opacity="0.95"/>
    <!-- Bill inner border -->
    <rect x="86" y="52" width="96" height="36" rx="4" ry="4" fill="none" stroke="#3e8b2f" stroke-width="2" opacity="0.6"/>
    <!-- Dollar sign -->
    <g transform="translate(126,74) scale(1.1)">
      <path d="M0 -10 C 6 -20, 18 -20, 18 -8 C 18 0, 6 6, 0 10 C -6 13, -6 16, 2 18"
            fill="none" stroke="#1d6a20" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
      <line x1="0" y1="-24" x2="0" y2="26" stroke="#1d6a20" stroke-width="2.2" stroke-linecap="round"/>
    </g>
  </g>

  <!-- Wallet body -->
  <g transform="translate(30,68)">
    <!-- main wallet shape -->
    <rect x="12" y="50" width="192" height="92" rx="12" ry="12" fill="url(#walletGrad)"/>

    <!-- wallet flap / top -->
    <path d="M12 62 L12 62 Q36 34 120 46 L204 56 L204 110 Q204 132 176 132 L36 132 Q12 132 12 110 Z"
          fill="#0a6a77" opacity="0.88"/>

    <!-- inner pocket cut -->
    <path d="M28 86 h140 a6 6 0 0 1 6 6 v20 a6 6 0 0 1 -6 6 H28 a6 6 0 0 1 -6 -6 v-20 a6 6 0 0 1 6 -6 z"
          fill="#0b5560" opacity="0.9"/>

    <!-- clasp / strap -->
    <rect x="160" y="82" width="46" height="34" rx="8" ry="8" fill="#0d6c7a"/>
    <circle cx="180" cy="99" r="7.5" fill="#0a3940"/>

    <!-- small stitched line (subtle) -->
    <path d="M28 90 h136" stroke="#0b8594" stroke-width="1.5" stroke-linecap="round" opacity="0.45"/>
  </g>

  <!-- topmost small bill peeking out (foreground) -->
  <g transform="translate(54,36) rotate(-6 60 20)">
    <rect x="42" y="-4" width="96" height="54" rx="6" ry="6" fill="url(#billGrad)"/>
    <rect x="54" y="6" width="72" height="36" rx="3" ry="3" fill="none" stroke="#2e7f35" stroke-width="2" opacity="0.7"/>
    <!-- small $ sign -->
    <g transform="translate(88,18) scale(0.95)">
      <path d="M0 -6 C 5 -12, 14 -12, 14 -5 C 14 2, 5 6, 0 10" fill="none" stroke="#16551b" stroke-width="2.2" stroke-linecap="round"/>
      <line x1="0" y1="-16" x2="0" y2="20" stroke="#16551b" stroke-width="1.5" stroke-linecap="round"/>
    </g>
  </g>

</svg>
