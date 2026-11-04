

import Alpine from 'alpinejs';
import { sidebarScroll } from './sidebar-scroll';

window.Alpine = Alpine;
Alpine.data('sidebarScroll', sidebarScroll);

Alpine.start();

import './echo';

import './realtime/orders';
