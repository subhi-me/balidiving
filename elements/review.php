<style>
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 1px 1px, #e8f1ff 1px, transparent 1px);
        background-size: 40px 40px;
        opacity: 0.5;
        pointer-events: none;
        z-index: -1;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        padding: 40px 20px;
        position: relative;
        z-index: 1;
    }

    .section-title {
        text-align: center;
        margin-bottom: 56px;
    }

    .section-title h1 {
        font-size: 2.5rem;
        color: #3552c8;
        margin-bottom: 8px;
    }

    .section-title p {
        font-size: 1.125rem;
        color: #5f6368;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(60,64,67,0.3), 0 4px 8px rgba(60,64,67,0.15);
        overflow: hidden;
        margin-bottom: 24px;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .card-body {
        padding: 32px;
    }

    .rating-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 24px;
        border-bottom: 1px solid #e8eaed;
        padding-bottom: 24px;
    }

    .rating {
        display: flex;
        gap: 4px;
    }

    .star {
        font-size: 20px;
        color: #fbbc04;
    }

    .rating-text {
        font-size: 14px;
        color: #5f6368;
        font-weight: 500;
    }

    .user-info {
        text-align: center;
        margin-bottom: 24px;
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #a2d2fa, #3552c8);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        color: white;
        font-size: 18px;
        margin-bottom: 8px;
    }

    .username {
        font-size: 16px;
        font-weight: 500;
        color: #202124;
    }

    .date {
        font-size: 14px;
        color: #5f6368;
    }

    .review {
        font-size: 15px;
        line-height: 1.8;
        color: #3c4043;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

    <div class="container">
        <div class="section-title">
            <h1>Customer Reviews</h1>
            <p>Real experiences from our diving community</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="rating-container">
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                            <span class="rating-text">5.0 out of 5</span>
                        </div>
                        
                        <div class="user-info">
                            <div class="avatar">JD</div>
                            <div class="user-details">
                                <div class="username">John Diver</div>
                                <p class="date">4 weeks ago</p>
                            </div>
                        </div>
                        
                        <p class="review">
                            Amazing diving shop. I did my first diving trip with them to Tulamben in January. Everything perfect. Kim was the guide, very friendly, very supportive and always showing the best of the underwater world. In May I came back to Bali and joined two diving trips again with Kim, first one to Amed and the second one to Padang Bay. Again an amazing experience. Thanks for everything and I will definitely book my next diving trip with you when I am back in Bali
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="rating-container">
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                            <span class="rating-text">5.0 out of 5</span>
                        </div>
                        
                        <div class="user-info">
                            <div class="avatar">SC</div>
                            <div class="user-details">
                                <div class="username">Sarah Chen</div>
                                <p class="date">3 weeks ago</p>
                            </div>
                        </div>
                        
                        <p class="review">
                            Amazing staffs, all friendly and funny. Get coffee or tea and sit down and relax. Did PADI certificate with them and it was successful. Completed the online exam at my own pace. The instructors have lots of experience also. Everything was well organized, transportation (always on time), lunch, sites restaurants, dive sites, etc. The diving equipment were up to date and working properly.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="rating-container">
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                            <span class="rating-text">5.0 out of 5</span>
                        </div>
                        
                        <div class="user-info">
                            <div class="avatar">MR</div>
                            <div class="user-details">
                                <div class="username">Mike Roberts</div>
                                <p class="date">2 weeks ago</p>
                            </div>
                        </div>
                        
                        <p class="review">
                            I had two fun dives for certified divers. My guide Chris was extremely friendly, helpful, and accommodating, and gave me plenty of constructive feedback on how to improve. The entire crew is very friendly and warm to me and made sure I had a lovely experience!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

