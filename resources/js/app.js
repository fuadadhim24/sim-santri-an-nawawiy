import './bootstrap';

// Include Livewire for Vite-based builds. This import is optional when using the
// @livewireScripts blade directive, but keeping it ensures that the Livewire
// client is bundled with the application's JS and avoids console errors when
// assets are cached.
import 'livewire';

import Swal from 'sweetalert2';
window.Swal = Swal;
