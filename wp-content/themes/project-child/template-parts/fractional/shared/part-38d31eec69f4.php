<?php defined( 'ABSPATH' ) || exit; ?>
<aside class="floating-contact-stack" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_4ff6a4a13bca', 'shared' ) ); ?>">
    
    <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="floating-btn floating-btn-zalo" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_11c56f1da0dd', 'shared' ) ); ?>">
      <span class="floating-tooltip"><?php echo project_child_fcc_text( 'fcc_ui_f31d233ccedb04', 'shared', false ); ?></span>
      <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z" fill="#0068FF"></path>
        <text x="12" y="14" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-size="7.2" font-weight="800" fill="#FFFFFF" text-anchor="middle" letter-spacing="-0.3">Zalo</text>
      </svg>
    </a>

    <a <?php project_child_fcc_contact_attributes( 'facebook' ); ?> target="_blank" rel="noopener noreferrer" class="floating-btn floating-btn-facebook" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_fb_label', 'shared' ) ?: 'Chat Facebook tư vấn' ); ?>">
      <span class="floating-tooltip"><?php echo esc_html( project_child_fcc_raw_text( 'fcc_ui_m_fb_tooltip', 'shared' ) ?: 'Chat Facebook' ); ?></span>
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 17.9895 4.38823 22.954 10.125 23.8542V15.4688H7.07812V12H10.125V9.35625C10.125 6.34875 11.9165 4.6875 14.6576 4.6875C15.9701 4.6875 17.3438 4.92188 17.3438 4.92188V7.875H15.8306C14.3399 7.875 13.875 8.80001 13.875 9.74902V12H17.2031L16.6711 15.4688H13.875V23.8542C19.6118 22.954 24 17.9895 24 12Z" fill="#1877F2"/>
        <path d="M16.6711 15.4688L17.2031 12H13.875V9.74902C13.875 8.80001 14.3399 7.875 15.8306 7.875H17.3438V4.92188C17.3438 4.92188 15.9701 4.6875 14.6576 4.6875C11.9165 4.6875 10.125 6.34875 10.125 9.35625V12H7.07812V15.4688H10.125V23.8542C10.7452 23.9515 11.3723 24 12 24C12.6277 24 13.2548 23.9515 13.875 23.8542V15.4688H16.6711Z" fill="#FFFFFF"/>
      </svg>
    </a>

    <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> class="floating-btn floating-btn-phone" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_e49c4bed29ca', 'shared' ) ); ?>">
      <span class="floating-tooltip"><?php echo project_child_fcc_text( 'fcc_ui_e2b5b5e41063f4', 'shared', false ); ?></span>
      <svg width="22" height="22" viewbox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
      </svg>
    </a>

    
    <button type="button" id="floating-scroll-top" class="floating-btn floating-btn-top" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_6a7a3ceef1f1', 'shared' ) ); ?>">
      <span class="floating-tooltip"><?php echo project_child_fcc_text( 'fcc_ui_c147e4bc37f96e', 'shared', false ); ?></span>
      <svg width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 15l-6-6-6 6"></path>
      </svg>
    </button>
  </aside>
