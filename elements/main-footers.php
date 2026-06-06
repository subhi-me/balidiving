<style>
    /* --- FOOTER STYLING --- */

    /* Main footer container styling */
    .custom-site-footer {
        background-color: #222222; /* A softer black */
        color: #a9a9a9; /* Grey text for better readability on dark background */
        font-family: 'Poppins', sans-serif; /* Modern, clean font */
        padding: 50px 0 30px 0;
        font-size: 15px;
        line-height: 1.7;
    }

    /* Flexbox container for the columns */
    .footer-container {
        display: flex;
        flex-wrap: wrap; /* Allows columns to stack on mobile */
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* General styling for each column */
    .footer-column {
        flex: 1; /* Allows columns to grow and shrink */
        min-width: 280px; /* Minimum width before stacking */
        padding: 0 15px;
        margin-bottom: 30px;
    }

    /* Column titles */
    .footer-column h3 {
        font-size: 18px;
        font-weight: 600;
        color: #FFFF00; /* Yellow accent color from your original design */
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

 
    
    /* Contact info labels */
    .footer-info p strong {
        color: #c9c9c9;
        font-weight: 600;
        margin-right: 8px;
    }

    /* Styling for links in the footer */
    .footer-column a {
        color: #a9a9a9;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-column a:hover {
        color: #FFFF00; /* Link color changes to yellow on hover */
    }

    /* Removes bullet points from the navigation list */
    .footer-links ul {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    /* Wrapper to keep the map responsive */
    .map-wrapper {
        position: relative;
        overflow: hidden;
        padding-top: 75%; /* 4:3 Aspect Ratio */
        border-radius: 8px;
    }

    .map-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        filter: grayscale(100%); /* Makes the map grey */
        transition: filter 0.4s ease;
    }

    .map-wrapper:hover iframe {
        filter: grayscale(0%); /* Map becomes colorful on hover */
    }

    /* Bottom section of the footer (copyright) */
    .footer-bottom {
        text-align: center;
        padding: 20px;
        margin-top: 20px;
        border-top: 1px solid #333; /* Separator line */
    }

    .footer-bottom p {
        margin: 0;
        font-size: 14px;
    }
    
    /* Responsive adjustments for mobile devices */
    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column; /* Stacks the columns vertically */
            align-items: center;
            text-align: center;
        }

        .footer-column h3::after {
            left: 50%;
            transform: translateX(-50%); /* Centers the decorative underline */
        }
    }
</style>
<section once="footers" class="cid-rqwoBIN68a" id="info3-a">
<footer class="custom-site-footer">
    <div class="footer-container">
        <div class="footer-column footer-info">
            <h3>Contact Us</h3>
            <p><strong>Address:</strong><br>Jl. By Pass Ngurah Rai No. 46E,<br> Sanur Kauh, Denpasar
            <p><strong>Phone:</strong><br> <a href="tel:+62361270791">+62 361 2707 91</a>
            <p><strong>WhatsApp:</strong><br> <a href="https://wa.me/6287861190174" target="_blank">+62 878 6119 0174</a>
            <p><strong>Email:</strong><br> <a href="mailto:sales@balidiving.com">sales@balidiving.com</a></p>
        </div>

        <div class="footer-column footer-links">
            <h3>Navigation</h3>
            <ul>
                <li><a href="https://balidiving.com/#">Home</a></li>
                <li><a href="https://balidiving.com/#package">Packages</a></li>
                <li><a href="https://booking.balidiving.com/pricelist/?utm_source=balidiving.com&utm_medium=web_footer&utm_campaign=pricelist">Pricelist</a></li>
                <li><a href="https://balidiving.com/snorkeling">Snorkeling</a></li>
                <li><a href="https://balidiving.com/discover-scuba-diving-in-bali">Diving Tours</a></li>
                <li><a href="https://balidiving.com/special-offers.php">Promotions</a></li>
            </ul>
        </div>

        <div class="footer-column footer-map">
            <h3>Our Location</h3>
          <a href="https://www.google.com/maps/dir//Bali+Diving,+Jl.+Bypass+Ngurah+Rai+No.46E,+Sanur+Kauh,+Denpasar+Selatan,+Denpasar+City,+Bali+80025/@-8.7045246,115.2526005,19z/data=!4m9!4m8!1m0!1m5!1m1!1s0x2dd241bc3e6d6237:0xda863183d7006424!2m2!1d115.2532456!2d-8.7045246!3e0?entry=ttu&g_ep=EgoyMDI1MDcyMy4wIKXMDSoASAFQAw%3D%3D" target="_blank">
            <img src="Images/main/bali-diving-map-location.jpg" alt="map bali diving"></a>
            </div>
        </div>
    </div>
    </section>

    <div class="footer-bottom">
        <p>&copy; 2025 Bali Diving. All rights reserved.</p>
    </div>
</footer>