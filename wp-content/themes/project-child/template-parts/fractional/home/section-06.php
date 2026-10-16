<?php
defined( 'ABSPATH' ) || exit;
$fcc_routine_video = get_post_meta( project_core_fcc_page_id( 'home' ), 'fcc_home_routine_video_url', true );
$fcc_routine_link = $fcc_routine_video ? esc_url_raw( $fcc_routine_video ) : project_child_fcc_contact_link( 'zalo' );
?>
<section class="section" id="routine">
      <div class="container">
        <div class="section-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_5069048586d7c3', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_1fa8ec67cb5a74', 'home' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_d84da31e7b2dba', 'home' ); ?></p>
        </div>

        
        <div class="brief-routine-grid">
          
          <article class="routine-column-card routine-card-day collapsed">
            <div class="routine-column-header">
              <div class="routine-badge-icon routine-badge-icon-day" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_6f8a79402827e4', 'shared' ) ); ?>">
                <svg width="30" height="30" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <circle cx="16" cy="16" r="14.5" stroke="#C5A059" stroke-width="1" stroke-opacity="0.45"></circle>
                  <circle cx="16" cy="16" r="10.5" stroke="#C5A059" stroke-width="1.2" stroke-dasharray="2 2"></circle>
                  <path d="M16 2.5L17.5 7.5L16 6.5L14.5 7.5L16 2.5Z" fill="#C5A059"></path>
                  <path d="M16 29.5L17.5 24.5L16 25.5L14.5 24.5L16 29.5Z" fill="#C5A059"></path>
                  <path d="M2.5 16L7.5 17.5L6.5 16L7.5 14.5L2.5 16Z" fill="#C5A059"></path>
                  <path d="M29.5 16L24.5 17.5L25.5 16L24.5 14.5L29.5 16Z" fill="#C5A059"></path>
                  <path d="M6.5 6.5L10.5 9L9.5 8L8 9.5L6.5 6.5Z" fill="#C5A059"></path>
                  <path d="M25.5 25.5L21.5 23L22.5 24L24 22.5L25.5 25.5Z" fill="#C5A059"></path>
                  <path d="M6.5 25.5L10.5 23L9.5 24L8 22.5L6.5 25.5Z" fill="#C5A059"></path>
                  <path d="M25.5 6.5L21.5 9L22.5 8L24 9.5L25.5 6.5Z" fill="#C5A059"></path>
                  <circle cx="16" cy="16" r="5" fill="#C5A059"></circle>
                  <circle cx="16" cy="16" r="2.5" fill="#FFF8E7"></circle>
                </svg>
              </div>
              <div class="routine-header-info">
                <span class="routine-header-tag"><?php echo project_child_fcc_text( 'fcc_ui_bbd8e843bc3326', 'home' ); ?></span>
                <h3><?php echo project_child_fcc_text( 'fcc_ui_3ca61cb5f92ad1', 'home' ); ?></h3>
                <span class="routine-header-sub"><?php echo project_child_fcc_text( 'fcc_ui_33b2876f22ec35', 'home' ); ?></span>
              </div>
              <button type="button" class="routine-toggle-btn" aria-expanded="false" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_56bfb850da0788', 'shared' ) ); ?>">
                <span class="toggle-text"><?php echo project_child_fcc_text( 'fcc_ui_msg_e30c937169', 'shared', false ); ?></span>
                <svg class="routine-toggle-icon" width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="4 10 8 6 12 10"></polyline>
                </svg>
              </button>
            </div>

            
            <div class="routine-collapsed-summary" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_df4b07da76fd42', 'shared' ) ); ?>">
              <strong><?php echo project_child_fcc_text( 'fcc_ui_a8547b35389863', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_4e0a8e13025a90', 'home' ); ?>
            </div>

            <div class="routine-steps-wrapper">
              <ul class="routine-steps-list">
              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_1150d5032f811b', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_52e17cb0deba38', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_0faf3028b04a6b', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_c04f4ad85f5d1e', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M8 1.5C8 1.5 3 7 3 10.5a5 5 0 0 0 10 0C13 7 8 1.5 8 1.5z"></path>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_a3fa1d9cfddbd7', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_dc7e187840ae1c', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_715f3de32d7627', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_cf91a6b6f3ebd9', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_2caf987401819e', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_51e5d06c4bd994', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_4b1b0491df1109', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_1a64c1577ac190', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="#C5A059">
                          <path d="M8 0L9.8 5.8L15.6 7.6L9.8 9.4L8 15.2L6.2 9.4L0.4 7.6L6.2 5.8L8 0Z"></path>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_508c5d6efc0914', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_e5928455565afc', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_42b60390b14af9', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_a10e07eb3754d6', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_44246cf815a4c5', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_3e2fcae617ec9f', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M8 1.5l5.5 2.5v4c0 3.8-2.5 6.2-5.5 7.5-3-1.3-5.5-3.7-5.5-7.5V4L8 1.5z"></path>
                          <path d="M6 8l1.5 1.5L10.5 6.5"></path>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_c9bfb772712c09', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_96d8a6bb4dc378', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_1f4c916e18b01b', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_6b7f58ab228d9e', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_b7e3dd1158d51b', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_a305d919504297', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round">
                          <circle cx="8" cy="8" r="3.2"></circle>
                          <line x1="8" y1="1" x2="8" y2="2.5"></line><line x1="8" y1="13.5" x2="8" y2="15"></line>
                          <line x1="1" y1="8" x2="2.5" y2="8"></line><line x1="13.5" y1="8" x2="15" y2="8"></line>
                          <line x1="3" y1="3" x2="4.2" y2="4.2"></line><line x1="11.8" y1="11.8" x2="13" y2="13"></line>
                          <line x1="3" y1="13" x2="4.2" y2="11.8"></line><line x1="11.8" y1="4.2" x2="13" y2="3"></line>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_8829cf33691b9b', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_51e9c718a35f5f', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </article>

        
        <article class="routine-column-card routine-card-night collapsed">
          <div class="routine-column-header">
            <div class="routine-badge-icon routine-badge-icon-night" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_9efecf6decbfe1', 'shared' ) ); ?>">
              <svg width="30" height="30" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="16" cy="16" r="14.5" stroke="#C5A059" stroke-width="1" stroke-opacity="0.3"></circle>
                <path d="M21 16.5C20.4 21.2 16.5 24.8 11.8 24.8C8.5 24.8 5.6 23 4 20.2C5.5 21.3 7.4 22 9.5 22C14.5 22 18.5 18 18.5 13C18.5 10.8 17.7 8.8 16.4 7.2C20 8.5 21.5 12.8 21 16.5Z" fill="#C5A059" stroke="#E5DDCB" stroke-width="0.8"></path>
                <path d="M22.5 5.5L23.4 8L26 8.9L23.4 9.8L22.5 12.5L21.6 9.8L19 8.9L21.6 8L22.5 5.5Z" fill="#F0DFC0"></path>
                <circle cx="26" cy="16" r="1" fill="#E5DDCB"></circle>
              </svg>
            </div>
            <div class="routine-header-info">
              <span class="routine-header-tag"><?php echo project_child_fcc_text( 'fcc_ui_f9b251f5e92008', 'home' ); ?></span>
              <h3><?php echo project_child_fcc_text( 'fcc_ui_9cc7af4413ee5a', 'home' ); ?></h3>
              <span class="routine-header-sub"><?php echo project_child_fcc_text( 'fcc_ui_9ba02722fc9988', 'home' ); ?></span>
            </div>
            <button type="button" class="routine-toggle-btn" aria-expanded="false" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_56bfb850da0788', 'shared' ) ); ?>">
              <span class="toggle-text"><?php echo project_child_fcc_text( 'fcc_ui_msg_e30c937169', 'shared', false ); ?></span>
              <svg class="routine-toggle-icon" width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="4 10 8 6 12 10"></polyline>
              </svg>
            </button>
          </div>

          
          <div class="routine-collapsed-summary" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_df4b07da76fd42', 'shared' ) ); ?>">
            <strong><?php echo project_child_fcc_text( 'fcc_ui_b4179608806e68', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_423d9f2734335f', 'home' ); ?>
          </div>

          <div class="routine-steps-wrapper">
            <ul class="routine-steps-list">
              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_1150d5032f811b', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_14b2deb9407a0b', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_166f5d3220ba9a', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_474e00dea5bed2', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M8 1.5C8 1.5 3 7 3 10.5a5 5 0 0 0 10 0C13 7 8 1.5 8 1.5z"></path>
                          <path d="M8 6.5v4M6.5 8.5H9.5"></path>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_ce873dba725167', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_34d868a51106b1', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_715f3de32d7627', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_a83c273956fe47', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_fd8cef5c370ae0', 'home' ); ?></div>
                    <p class="routine-step-howto">
                      • <em><?php echo project_child_fcc_text( 'fcc_ui_324d7d6b400595', 'home' ); ?></em> <?php echo project_child_fcc_text( 'fcc_ui_2c1c3d6e8ddd5f', 'home' ); ?><br>
                      • <em><?php echo project_child_fcc_text( 'fcc_ui_969271ba333dfa', 'home' ); ?></em> <?php echo project_child_fcc_text( 'fcc_ui_f2c5feb4cea311', 'home' ); ?>
                    </p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M13.5 9.5A5.5 5.5 0 1 1 6.5 2.5a4.5 4.5 0 0 0 7 7z"></path>
                          <circle cx="12.5" cy="3.5" r="0.8" fill="#C5A059"></circle>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_cc4571efa0412e', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_bb2d5539d6b824', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_42b60390b14af9', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_3bf276be1945ac', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_c7affcf7abc1a0', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_f0881d05c30652', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M8 14s5-2.5 5-7.5C13 3 8 2 8 2S3 3 3 6.5C3 11.5 8 14 8 14z"></path>
                          <path d="M8 5v6"></path>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_6e913bd4e6c3f2', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_5b5a0f9a1ed2ec', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>

              
              <li class="routine-step-card-item">
                <div class="routine-step-head">
                  <div class="routine-step-tag-row">
                    <span class="routine-step-badge"><?php echo project_child_fcc_text( 'fcc_ui_1f4c916e18b01b', 'home' ); ?></span>
                    <span class="routine-step-label"><?php echo project_child_fcc_text( 'fcc_ui_5b96c21053a95f', 'home' ); ?></span>
                  </div>
                </div>
                <div class="routine-step-body">
                  <div class="routine-step-content">
                    <div class="routine-step-product-title"><?php echo project_child_fcc_text( 'fcc_ui_9ab417ab0fa52a', 'home' ); ?></div>
                    <p class="routine-step-howto"><?php echo project_child_fcc_text( 'fcc_ui_c0732fa1d841b5', 'home' ); ?></p>
                    <div class="routine-step-technique">
                      <span class="tech-icon">
                        <svg width="13" height="13" viewbox="0 0 16 16" fill="#C5A059">
                          <polygon points="8,1 10,6 15,8 10,10 8,15 6,10 1,8 6,6"></polygon>
                        </svg>
                      </span>
                      <span><span class="technique-tag"><?php echo project_child_fcc_text( 'fcc_ui_07e520da0d6f38', 'home' ); ?></span> <?php echo project_child_fcc_text( 'fcc_ui_a68cbd7823a42a', 'home' ); ?></span>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </article>
      </div>

        
        <div class="routine-demo-banner">
          
          <div class="demo-player-card">
            <div class="demo-player-preview"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_555650f3e397c2' ); ?>>
              <?php
              echo project_child_fcc_picture_html(
                  project_child_fcc_image( 'fcc_ui_555650f3e397c2' ),
                  project_child_fcc_image_alt( 'fcc_ui_555650f3e397c2', 'fcc_ui_6df4668d328d6b' ),
                  'demo-player-img',
                  array( 'width' => 600, 'height' => 340, 'loading' => 'lazy' )
              );
              ?>
              <div class="demo-player-overlay">
                <a <?php echo $fcc_routine_link ? 'href="' . esc_url( $fcc_routine_link ) . '"' : 'hidden'; ?> target="_blank" rel="noopener noreferrer" class="demo-play-btn-wrap" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_7273916d822930', 'shared' ) ); ?>">
                  <div class="demo-play-btn">
                    <svg width="22" height="22" viewbox="0 0 24 24" fill="currentColor">
                      <polygon points="6,3 20,12 6,21"></polygon>
                    </svg>
                  </div>
                </a>
              </div>
            </div>
            <div class="demo-player-footer">
              <div>
                <div class="demo-player-caption"><?php echo project_child_fcc_text( 'fcc_ui_c70f7bac8522f7', 'home' ); ?></div>
              </div>
              <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="font-size: 12px; padding: 7px 14px;">
                <?php echo project_child_fcc_text( 'fcc_ui_4c38e0fae9a6b0', 'home', false ); ?>
              </a>
            </div>
          </div>

          
          <div class="demo-tips-card">
            <div>
              <h3 class="demo-tips-title"><?php echo project_child_fcc_text( 'fcc_ui_0c6cfecb6d2c1d', 'home' ); ?></h3>
              <p class="demo-tips-sub"><?php echo project_child_fcc_text( 'fcc_ui_9a91ae567b1c6d', 'home' ); ?></p>
              
              <ul class="demo-rules-list">
                <li class="demo-rule-item">
                  <div class="demo-rule-icon">1</div>
                  <div class="demo-rule-text">
                    <strong><?php echo project_child_fcc_text( 'fcc_ui_1d369fe064bf5f', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_df627228705065', 'home' ); ?>
                  </div>
                </li>
                <li class="demo-rule-item">
                  <div class="demo-rule-icon">2</div>
                  <div class="demo-rule-text">
                    <strong><?php echo project_child_fcc_text( 'fcc_ui_0ba4bb71e9634f', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_695f6c07cc3bce', 'home' ); ?>
                  </div>
                </li>
                <li class="demo-rule-item">
                  <div class="demo-rule-icon">3</div>
                  <div class="demo-rule-text">
                    <strong><?php echo project_child_fcc_text( 'fcc_ui_c20f5e21b85662', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_5bf565980d9ff8', 'home' ); ?>
                  </div>
                </li>
              </ul>
            </div>

            <div style="text-align: center;">
              <a class="btn btn-gold" <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" style="width: 100%; text-align: center; font-size: 13.5px; padding: 13px 20px;">
                <?php echo project_child_fcc_text( 'fcc_ui_245d74f1632439', 'home', false ); ?>
              </a>
            </div>
          </div>
        </div>

        <p class="disclaimer-note" style="margin-top: 24px;">
          <?php echo project_child_fcc_text( 'fcc_ui_3dcdc80e3c2a9a', 'home' ); ?>
        </p>
      </div>
    </section>
