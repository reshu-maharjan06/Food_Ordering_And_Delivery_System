<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni - Authentic Nepali Experience</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Lato:wght@300;400;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pages/landing.css">
</head>

<body>

    <!-- NAVBAR -->
    <header class="nav-wrapper" id="sticky-nav">
        <a href="landing.php" class="logo">
            <img src="assets/img/logo.svg" alt="sauni">
        </a>
        <nav>
            <ul>
                <li><a href="landing.php" class="active">Home</a></li>
                <li><a href="#about">Heritage</a></li>
                <li><a href="customer/browse.php">Browse</a></li>
                <li><a href="our-story.php">Our Story</a></li>
            </ul>
        </nav>

        <div class="nav-right">
            <button class="icon-btn cart" id="cartPill" onclick="location.href='customer/cart.php'">
                <svg viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM7 9a1 1 0 0 1-1-1 1 1 0 0 1 1-1 1 1 0 0 1 1 1 1 1 0 0 1-1 1zm10 0a1 1 0 0 1-1-1 1 1 0 0 1 1-1 1 1 0 0 1 1 1 1 1 0 0 1-1 1zM11 4h2v2h-2z" />
                </svg>
                <span class="badge" data-cart-count>0</span>
            </button>

            <?php if($isLoggedIn): ?>
            <div class="profile-wrap" id="profile-section">
                <?php if (!empty($_SESSION['profile_pic'])): ?>
                    <button class="profile-btn" id="profBtn" style="background-image:url('<?= $_SESSION['profile_pic'] ?>'); background-size:cover; background-position:center; font-size:0; border:2px solid #fff;"></button>
                <?php else: ?>
                    <button class="profile-btn" id="profBtn">
                        <?= e(strtoupper(substr($username,0,1))); ?>
                    </button>
                <?php endif; ?>
                <div class="profile-dropdown" id="profileDrop">
                    <div class="pd-header">
                        <div class="pd-name"><?php echo e($username); ?></div>
                        <div class="pd-role"><?php echo strtoupper($role); ?></div>
                    </div>
                    
                    <?php if($role==='customer'): ?>
                    <a href="customer/profile.php" class="pd-link">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> My Profile
                    </a>
                    <a href="customer/history.php" class="pd-link">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> My Orders
                    </a>
                    <a href="customer/tracker.php" class="pd-link">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Track Order
                    </a>
                    <?php elseif($role==='admin'): ?>
                    <a href="admin/dashboard.php" class="pd-link">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Admin Dash
                    </a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="pd-link danger">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg> Sign out
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="auth-btns" id="auth-section">
                <a href="signup.php" class="auth-link">Sign up</a>
                <a href="index.php" class="btn-pill btn-pill--orange">
                    Login
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- FULL PAGE HERO -->
    <section class="hero-section">
        <div class="dark-sweep"></div>
        <img src="assets/img/foods/samaybaji.png" alt="Main Bowl" class="hero-bowl" id="heroImage">

        <main class="hero">
            <div class="hero-left reveal-up">
                <h1><span class="bold">Order your</span> <span class="light">favourite Foods</span></h1>
                <p class="desc">
                    Experience the true, unfiltered essence of Nepal. From fire-roasted street delicacies to
                    centuries-old Himalayan heritage dishes, fuel your hunger with authentic bold flavors delivered
                    fresh.
                </p>

                <div class="total-wrapper">
                    <span class="lbl">Total order :</span>
                    <span class="val" id="totalPrice">Rs. 0</span>
                </div>

                <div class="controls">
                    <div class="qty-pill">
                        <button class="qty-btn" aria-label="Decrease" onclick="updateQty(-1)">
                            <svg viewBox="0 0 24 24">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                        <span class="qty-val" id="qtyVal">1</span>
                        <button class="qty-btn" aria-label="Increase" onclick="updateQty(1)">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                    </div>

                    <button class="btn-buy" id="order-now-btn" onclick="handleOrderNow(event)">
                        <div class="icon">
                            <svg viewBox="0 0 448 512">
                                <path d="M160 112c0-35.3 28.7-64 64-64s64 28.7 64 64v48H160V112zm-48 48H48c-26.5 0-48 21.5-48 48V416c0 53 43 96 96 96H352c53 0 96-43 96-96V208c0-26.5-21.5-48-48-48H336V112C336 50.1 285.9 0 224 0S112 50.1 112 112v48zm128 72c8.8 0 16-7.2 16-16s-7.2-16-16-16s-16 7.2-16 16s7.2 16 16 16zM136 216c0-8.8-7.2-16-16-16s-16 7.2-16 16s7.2 16 16 16s16-7.2 16-16z" />
                            </svg>
                        </div>
                        Order Now
                    </button>
                </div>

                <div class="carousel">
                    <button class="arrow-nav" onclick="scrollHeroPills(-1)">&lt;</button>
                    <div class="pill-wrap" id="heroPillWrap">
                        <div class="f-pill bg-green" onclick="pickHeroItem(this, 11, 375, 'assets/img/foods/Newari/choila.webp', 'Haku Choila', 'Spicy and savory marinated buffalo, a true Newari classic.')">
                            <div class="thumb"><img src="assets/img/foods/Newari/choila.webp" alt="HakuChoila"></div>
                            <div class="title">Haku Choila</div>
                            <div class="price"><span>Rs. </span>375</div>
                        </div>
                        <div class="f-pill bg-pink" onclick="pickHeroItem(this, 4, 250, 'assets/img/foods/Newari/yomari.webp', 'Yomari', 'Steamed pointed dumplings with molasses and sesame filling.')">
                            <div class="thumb"><img src="assets/img/foods/Newari/yomari.webp" alt="Yomari"></div>
                            <div class="title">Yomari</div>
                            <div class="price"><span>Rs. </span>250</div>
                        </div>
                        <div class="f-pill bg-peach active" onclick="pickHeroItem(this, 1, 445, 'assets/img/foods/samaybaji.png', 'Samay Baji', 'The ultimate Newari feast: beaten rice, smoked meat, lentils.')">
                            <div class="thumb"><img src="assets/img/foods/samaybaji.png" alt="Samay Baji"></div>
                            <div class="title">Samay Baji</div>
                            <div class="price"><span>Rs. </span>445</div>
                        </div>
                        <div class="f-pill bg-blue" onclick="pickHeroItem(this, 14, 280, 'assets/img/foods/Tharu/Chichar.png', 'Chichar', 'Traditional puffed rice snack from the Tharu community.')">
                            <div class="thumb"><img src="assets/img/foods/Tharu/Chichar.png" alt="Chichar"></div>
                            <div class="title">Chichar</div>
                            <div class="price"><span>Rs. </span>280</div>
                        </div>
                        <div class="f-pill bg-yellow" onclick="pickHeroItem(this, 7, 250, 'assets/img/foods/Tharu/dhikri.jpg', 'Dhikri', 'Traditional Tharu steamed rice dumplings, smooth and comforting.')">
                            <div class="thumb"><img src="assets/img/foods/Tharu/dhikri.jpg" alt="Dhikri"></div>
                            <div class="title">Dhikri</div>
                            <div class="price"><span>Rs. </span>250</div>
                        </div>
                    </div>
                    <button class="arrow-nav" onclick="scrollHeroPills(1)">&gt;</button>
                </div>
            </div>

            <div class="hero-right"></div>
        </main>
    </section>

    <!-- MARQUEE BANNER -->
    <div class="marquee-strip">
        <div class="marquee-content">
            <span>AUTHENTIC NEPALI FLAVORS</span><span class="dot">•</span>
            <span>WOOD FIRED SPECIALTIES</span><span class="dot">•</span>
            <span>TRUE HIMALAYAN SPICES</span><span class="dot">•</span>
            <span>EXPERT CULINARY MASTERY</span><span class="dot">•</span>
            <span>AUTHENTIC NEPALI FLAVORS</span><span class="dot">•</span>
            <span>WOOD FIRED SPECIALTIES</span><span class="dot">•</span>
            <span>TRUE HIMALAYAN SPICES</span><span class="dot">•</span>
            <span>EXPERT CULINARY MASTERY</span><span class="dot">•</span>
        </div>
    </div>

    <!-- ABOUT SECTION -->
    <section id="about" class="brutalist-about">
        <h1 class="bg-text">SAUNI</h1>
        <div class="b-left reveal-up">
            <span class="b-badge">EST. 2026</span>
            <h2>We don't just cook.<br>We craft culture.</h2>
            <p>Our kitchen celebrates the intense, diverse flavors of Nepal. From the fiery precision of Newari street
                food to genuine Himalayan spices. We source directly from local farmers, executing ancestral recipes
                with modern brutality.</p>
            <p>Discover hand-pulled noodles, wood-smoked meats, and the true essence of traditional dining delivered
                right to your door.</p>
            <a href="our-story.php" class="btn-harsh">Read Our Story <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg></a>
        </div>
        <div class="b-right reveal-up" style="transition-delay: 0.2s;">
            <img src="assets/img/foods/allinone.png" alt="Kitchen">
        </div>
    </section>

    <!-- EXPLORE BY CUISINE -->
    <section id="menu" class="explore-section">
        <div class="clean-head reveal-up">
            <h2>Explore by Cuisine</h2>
            <p>From centuries-old heritage dishes to modern Nepali fusion, discover authentic flavors crafted with
                purpose from every corner of Nepal.</p>
        </div>
        <div class="clean-scroll-wrap">
            <button class="scroll-arrow prev" onclick="scrollClean(-1)"><svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg></button>
            <div class="clean-grid" id="clean-grid-el">
                <?php foreach($menu_items as $index => $item): ?>
                <div class="premium-card reveal-up">
                    <div class="pm-img-wrap" onclick="window.location.href='customer/browse.php'">
                        <div class="pm-top-bar">
                            <span class="pm-tag"><?php echo htmlspecialchars($item['category'] ?? 'Special'); ?></span>
                            <div class="pm-rating">
                                <svg viewBox="0 0 576 512"><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                <?php echo number_format((float)($item['rating'] ?? 4.8), 1); ?>
                            </div>
                        </div>
                        <img src="<?php 
                            $img_path = $item['image_url'] ?? 'assets/img/default-food.jpg';
                            // Normalize path to root if it contains relative parent markers
                            $img_path = str_replace('../', '', $img_path);
                            echo htmlspecialchars($img_path); 
                        ?>" onerror="this.src='assets/img/default-food.jpg'" alt="Food image">
                    </div>
                    <div class="pm-body">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description'] ?? 'Authentic and freshly prepared local dish ready for immediate order.'); ?></p>
                    </div>
                    <div class="pm-foot">
                        <div class="pm-price"><span>Rs.</span> <?php echo number_format($item['price']); ?></div>
                        <button class="pm-action" onclick="window.location.href='customer/browse.php'">View More 
                            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="scroll-arrow next" onclick="scrollClean(1)"><svg viewBox="0 0 24 24">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg></button>
        </div>
    </section>

    <!-- CHEFS SECTION -->
    <section id="chefs" class="chefs-wrapper">
        <div class="c-head reveal-up">
            <h2>MASTERS<br>OF FIRE.</h2>
        </div>
        <div class="chef-reel">
            <div class="chef-cine reveal-up">
                <img src="assets/img/chefs/chef1.png" alt="Chef Kripa">
                <div class="chef-tag">
                    <h3>Kripa S.</h3>
                    <p>Newari Specialist</p>
                </div>
            </div>
            <div class="chef-cine reveal-up" style="transition-delay: 0.1s;">
                <img src="assets/img/chefs/chef2.png" alt="Chef Anil">
                <div class="chef-tag">
                    <h3>Anil G.</h3>
                    <p>Head Chef & Roaster</p>
                </div>
            </div>
            <div class="chef-cine reveal-up" style="transition-delay: 0.2s;">
                <img src="assets/img/chefs/chef3.jpg" alt="Chef Sunil">
                <div class="chef-tag">
                    <h3>Sunil T.</h3>
                    <p>Himalayan Spices</p>
                </div>
            </div>
            <div class="chef-cine reveal-up" style="transition-delay: 0.3s;">
                <img src="assets/img/chefs/chef4.png" alt="Chef Maya">
                <div class="chef-tag">
                    <h3>Sunita G.</h3>
                    <p>Magar Traditions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="classic-footer">
        <div class="footer-grid">
            <div class="f-brand">
                <h2 class="logo">sa<span>u</span>ni</h2>
                <p>Authentic Nepali foods delivered hot and fresh directly to your door.</p>
            </div>
            <div class="f-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="landing.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#chefs">Chefs</a></li>
                </ul>
            </div>
            <div class="f-contact">
                <h3>Contact Us</h3>
                <p>Kathmandu, Nepal</p>
                <p>Phone: +977 123 456 789</p>
                <p>Email: order@sauni.com</p>
            </div>
        </div>
        <div class="f-bottom">
            <p>© 2026 The Sauni Initiative. All rights reserved.</p>
        </div>
    </footer>

    <div class="food-modal" id="foodModal" onclick="if(event.target===this)closeFoodModal()">
        <div class="fm-card">
            <button class="fm-close" onclick="closeFoodModal()"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            <div class="fm-img" id="fmImg"></div>
            <div class="fm-content">
                <span class="fm-cat">Sauni Select</span>
                <h2 class="fm-title" id="fmTitle">Food Item</h2>
                <p class="fm-desc" id="fmDesc">This is a beautiful description of the authentic food item.</p>
                <div class="fm-price" id="fmPrice"><span>Rs.</span> 000</div>
                <button class="fm-btn" id="fmAddBtn">
                    <svg viewBox="0 0 448 512"><path d="M160 112c0-35.3 28.7-64 64-64s64 28.7 64 64v48H160V112zm-48 48H48c-26.5 0-48 21.5-48 48V416c0 53 43 96 96 96H352c53 0 96-43 96-96V208c0-26.5-21.5-48-48-48H336V112C336 50.1 285.9 0 224 0S112 50.1 112 112v48zm128 72c8.8 0 16-7.2 16-16s-7.2-16-16-16s-16 7.2-16 16s7.2 16 16 16zM136 216c0-8.8-7.2-16-16-16s-16 7.2-16 16s7.2 16 16 16s16-7.2 16-16z"/></svg>
                    Add to Cart
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/sauni-cart.js"></script>
    <script src="assets/js/pages/landing.js"></script>
</body>

</html>

