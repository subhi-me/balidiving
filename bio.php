<?php
if (file_exists('template/seo_manager.php')) {
  require_once 'template/seo_manager.php';
} else {
  function generate_seo_tags($page)
  {
    return "<title>Bali Diving Bio</title>";
  }
}
$page = 'bio'; // set page identifier
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php echo generate_seo_tags($page); ?>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://balidiving.com/bd192x192.png">
  <link rel="apple-touch-icon" href="https://balidiving.com/bd192x192.png"><!-- Schema.org Structured Data -->
  <!-- Speed Optimization: Resource Hints -->
  <link rel="preconnect" href="https://balidiving.com">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link rel="preconnect" href="https://www.googletagmanager.com">
  <link rel="preconnect" href="https://connect.facebook.net">
  <link rel="dns-prefetch" href="https://balidiving.com">

  <!-- Preload Critical Assets -->
  <link rel="preload" href="https://balidiving.com/logo-balidiving-250.jpg" as="image">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">

  <!-- Non-blocking Link Loading -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print"
    onload="this.media='all'">
  <noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </noscript>
  <style>
    body {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      height: 100%;
      width: 100%;
    }

    *,
    *::before,
    *::after {
      box-sizing: inherit;
    }

    html {
      height: 100%;
      width: 100%;
    }

    .bio-wrapper {
      width: 100%;
      min-height: 100%;
      background: linear-gradient(135deg, #0a74da 0%, #0d47a1 100%);
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 0;
      overflow-y: auto;
      position: relative;
    }

    .bio-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      opacity: 0.3;
      z-index: 0;
    }

    .bio-content {
      width: 100%;
      max-width: 680px;
      padding: 48px 24px 48px 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 1;
      padding-bottom: 80px;
      /* Add space for sticky social bar */
    }

    .profile-header {
      text-align: center;
      margin-bottom: 36px;
      width: 100%;
    }

    .avatar-circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      border: 4px solid rgba(255, 255, 255, 0.3);
      overflow: hidden;
    }

    .avatar-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .avatar-circle i {
      font-size: 56px;
      color: #0a74da;
    }

    .company-title {
      font-size: 36px;
      font-weight: 800;
      color: white;
      margin: 0 0 8px 0;
      letter-spacing: 2px;
      line-height: 1.1;
      text-transform: uppercase;
    }

    .business-name {
      font-size: 22px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.95);
      margin: 0 0 10px 0;
      letter-spacing: -0.3px;
      line-height: 1.3;
    }

    .business-tagline {
      font-size: 16px;
      color: rgba(255, 255, 255, 0.9);
      margin: 0 0 20px 0;
      font-weight: 500;
      line-height: 1.4;
    }

    .business-details {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 20px 24px;
      margin-top: 8px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .detail-row {
      display: flex;
      align-items: center;
      color: white;
      font-size: 15px;
      margin: 12px 0;
      line-height: 1.5;
    }

    .detail-row:first-child {
      margin-top: 0;
    }

    .detail-row:last-child {
      margin-bottom: 0;
    }

    .detail-row i {
      width: 24px;
      font-size: 16px;
      margin-right: 12px;
      text-align: center;
      flex-shrink: 0;
    }

    .location-link {
      color: white;
      text-decoration: none;
      transition: all 0.2s ease;
      border-bottom: 1px solid transparent;
    }

    .location-link:hover {
      color: rgba(255, 255, 255, 1);
      border-bottom: 1px solid rgba(255, 255, 255, 0.6);
    }

    .link-container {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 8px;
    }

    .action-link {
      background: white;
      color: #1a1a1a;
      text-decoration: none;
      padding: 14px 20px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      font-weight: 600;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      width: 100%;
      border: 2px solid transparent;
    }

    .action-link.featured-link {
      padding: 20px 28px;
      font-size: 18px;
      font-weight: 700;
      border-radius: 16px;
      background: linear-gradient(to right, #ffffff, #f0f9ff);
      border-color: rgba(10, 116, 218, 0.2);
    }

    .action-link:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
      border-color: rgba(10, 116, 218, 0.3);
    }

    .action-link:active {
      transform: translateY(-1px);
    }

    .action-link i {
      margin-right: 14px;
      font-size: 22px;
      color: #0a74da;
      flex-shrink: 0;
    }

    .social-media {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: rgba(10, 116, 218, 0.9);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-top: 1px solid rgba(255, 255, 255, 0.2);
      padding: 10px 0;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 16px;
      z-index: 1000;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    }

    .social-icon {
      width: 32px;
      height: 32px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .social-icon:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    }

    .social-icon:active {
      transform: translateY(-1px);
    }

    .social-icon i {
      font-size: 15px;
      color: #0d47a1;
    }

    .social-icon.instagram:hover i {
      color: #E4405F;
    }

    .social-icon.facebook:hover i {
      color: #1877F2;
    }

    .social-icon.tiktok:hover i {
      color: #000000;
    }

    .social-icon.whatsapp:hover i {
      color: #25D366;
    }

    .social-icon.whatsapp {
      width: 44px;
      height: 44px;
      background: #25D366;
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
      animation: pulse-green 2s infinite;
    }

    .social-icon.whatsapp i {
      color: white;
      font-size: 22px;
    }

    .social-icon.whatsapp:hover {
      background: #20ba5a;
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
      animation: none;
    }

    /* Desktop optimizations */
    @media (min-width: 768px) {
      .bio-content {
        padding: 60px 32px 60px 32px;
      }

      .avatar-circle {
        width: 140px;
        height: 140px;
        margin-bottom: 28px;
      }

      .avatar-circle i {
        font-size: 64px;
      }

      .company-title {
        font-size: 42px;
        margin-bottom: 10px;
      }

      .business-name {
        font-size: 26px;
        margin-bottom: 12px;
      }

      .business-tagline {
        font-size: 18px;
        margin-bottom: 24px;
      }

      .business-details {
        padding: 24px 28px;
      }

      .detail-row {
        font-size: 16px;
        margin: 14px 0;
      }

      .link-container {
        gap: 16px;
        margin-top: 12px;
      }

      .action-link {
        padding: 16px 24px;
        font-size: 16px;
        border-radius: 16px;
      }

      .action-link.featured-link {
        padding: 24px 32px;
        font-size: 20px;
        border-radius: 20px;
      }

      .action-link i {
        font-size: 24px;
        margin-right: 16px;
      }

      .social-media {
        padding: 12px 0;
        gap: 18px;
      }

      .social-icon {
        width: 38px;
        height: 38px;
      }

      .social-icon i {
        font-size: 18px;
      }

      .social-icon.whatsapp {
        width: 50px;
        height: 50px;
      }

      .social-icon.whatsapp i {
        font-size: 26px;
      }

      #faq-about,
      #planning-support-choice {
        margin-bottom: 60px;
      }
    }

    /* Large desktop */
    @media (min-width: 1024px) {
      .bio-content {
        padding: 80px 40px 80px 40px;
      }

      .avatar-circle {
        width: 150px;
        height: 150px;
      }

      .avatar-circle i {
        font-size: 70px;
      }

      .company-title {
        font-size: 48px;
      }

      .business-name {
        font-size: 28px;
      }

      .business-tagline {
        font-size: 19px;
      }

      .social-icon {
        width: 42px;
        height: 42px;
      }

      .social-icon i {
        font-size: 20px;
      }

      .social-icon.whatsapp {
        width: 56px;
        height: 56px;
      }

      .social-icon.whatsapp i {
        font-size: 30px;
      }
    }

    /* Small mobile */
    @media (max-width: 375px) {
      .bio-content {
        padding: 40px 20px 75px 20px;
      }

      .avatar-circle {
        width: 100px;
        height: 100px;
      }

      .avatar-circle i {
        font-size: 48px;
      }

      .company-title {
        font-size: 30px;
      }

      .business-name {
        font-size: 19px;
      }

      .business-tagline {
        font-size: 14px;
      }

      .business-details {
        padding: 18px 20px;
      }

      .detail-row {
        font-size: 14px;
      }

      .action-link {
        padding: 14px 20px;
        font-size: 15px;
      }

      .action-link.featured-link {
        padding: 18px 24px;
        font-size: 17px;
      }

      .action-link i {
        font-size: 20px;
      }
    }

    /* Tab Navigation Styles */
    .tab-navigation {
      display: flex;
      gap: 12px;
      width: 100%;
      margin-top: 8px;
      margin-bottom: 20px;
    }

    .tab-button {
      flex: 1;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.2);
      padding: 16px 20px;
      border-radius: 14px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .tab-button:hover {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.4);
      transform: translateY(-2px);
    }

    .tab-button.active {
      background: white;
      color: #0a74da;
      border-color: white;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .tab-button i {
      font-size: 18px;
    }

    /* Tab Content Styles */
    .tab-content-wrapper {
      width: 100%;
      position: relative;
    }

    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease-in-out;
    }

    .tab-content.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Email Form Styles */
    .email-form-container {
      width: 100%;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 28px 24px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .form-title {
      font-size: 24px;
      font-weight: 700;
      color: white;
      margin: 0 0 8px 0;
      text-align: center;
    }

    .form-subtitle {
      font-size: 15px;
      color: rgba(255, 255, 255, 0.9);
      margin: 0 0 24px 0;
      text-align: center;
    }

    .department-selector {
      margin-bottom: 20px;
    }

    .department-selector label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 15px;
      font-weight: 600;
      color: white;
      margin-bottom: 10px;
    }

    .department-selector label i {
      font-size: 16px;
    }

    .department-selector select {
      width: 100%;
      padding: 14px 16px;
      border-radius: 12px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.95);
      color: #1a1a1a;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      font-family: inherit;
    }

    .department-selector select:focus {
      outline: none;
      border-color: white;
      box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
    }

    .department-selector select:hover {
      border-color: rgba(255, 255, 255, 0.5);
    }

    .email-form-frame {
      border-radius: 12px;
      overflow: hidden;
      background: white;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .email-form-content {
      padding: 28px 24px;
    }

    .contact-form {
      width: 100%;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .form-group label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }

    .form-group label i {
      font-size: 14px;
      color: #0a74da;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.2s ease;
      background: #f8f9fa;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #0a74da;
      background: white;
      box-shadow: 0 0 0 4px rgba(10, 116, 218, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: #999;
    }

    .selected-dept-info {
      background: linear-gradient(135deg, #0a74da 0%, #0d47a1 100%);
      color: white;
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
    }

    .selected-dept-info i {
      font-size: 16px;
    }

    .selected-dept-info strong {
      font-weight: 700;
    }

    .form-actions {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .send-btn {
      width: 100%;
      padding: 16px 24px;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-family: inherit;
    }

    .send-btn i {
      font-size: 18px;
    }

    .send-btn:hover {
      transform: translateY(-2px);
    }

    .send-btn:active {
      transform: translateY(0);
    }

    .send-email {
      background: #0a74da;
      color: white;
      box-shadow: 0 4px 12px rgba(10, 116, 218, 0.3);
    }

    .send-email:hover {
      background: #0d47a1;
      box-shadow: 0 6px 20px rgba(10, 116, 218, 0.4);
    }

    .send-whatsapp {
      background: #25D366;
      color: white;
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
      animation: pulse-green 2s infinite;
    }

    .send-whatsapp:hover {
      background: #20ba5a;
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
      animation: none;
    }

    @keyframes pulse-green {
      0% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
      }
    }

    .send-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    .send-btn.loading {
      position: relative;
      color: transparent;
    }

    .send-btn.loading::after {
      content: "";
      position: absolute;
      width: 20px;
      height: 20px;
      top: 50%;
      left: 50%;
      margin-left: -10px;
      margin-top: -10px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }




    /* Responsive adjustments for tabs */
    @media (min-width: 768px) {
      .tab-button {
        padding: 18px 24px;
        font-size: 17px;
      }

      .tab-button i {
        font-size: 19px;
      }

      .email-form-container {
        padding: 32px 28px;
      }

      .form-title {
        font-size: 26px;
      }

      .form-subtitle {
        font-size: 16px;
      }

      .email-form-content {
        padding: 32px 28px;
      }

      .form-actions {
        flex-direction: row;
      }

      .send-btn {
        flex: 1;
      }
    }

    @media (max-width: 375px) {
      .tab-button {
        padding: 14px 16px;
        font-size: 14px;
      }

      .tab-button i {
        font-size: 16px;
      }

      .email-form-container {
        padding: 20px 18px;
      }

      .form-title {
        font-size: 20px;
      }

      .form-subtitle {
        font-size: 14px;
      }
    }

    .link-group-row {
      display: flex;
      flex-direction: row;
      gap: 10px;
      width: 100%;
    }

    .link-group-row .action-link {
      flex: 1;
      padding: 12px 5px;
      flex-direction: column;
      justify-content: center;
      text-align: center;
      gap: 5px;
      font-size: 13px;
    }

    .link-group-row .action-link i {
      margin-right: 0;
      font-size: 20px;
      margin-bottom: 4px;
    }

    /* Typeform Style Components - Premium Polish */
    .tf-container {
      width: 100%;
      min-height: 420px;
      display: flex;
      flex-direction: column;
      gap: 24px;
      position: relative;
    }

    /* Progress Indicator */
    .tf-progress-bar {
      width: 100%;
      height: 4px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
      margin-bottom: 20px;
      overflow: hidden;
      position: relative;
    }

    .tf-progress-value {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background: #25D366;
      /* WhatsApp Green for consistency */
      width: 20%;
      transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 0 10px rgba(37, 211, 102, 0.5);
    }

    .tf-step {
      display: none;
      width: 100%;
    }

    .tf-step.active {
      display: flex;
      flex-direction: column;
      gap: 16px;
      animation: tf-slideIn 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    @keyframes tf-slideIn {
      from {
        opacity: 0;
        transform: translateX(30px);
        filter: blur(5px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
        filter: blur(0);
      }
    }

    .tf-question {
      font-size: 26px;
      font-weight: 800;
      color: white;
      margin-bottom: 16px;
      line-height: 1.2;
      text-align: left;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .tf-choice {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 16px 20px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      gap: 16px;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      color: white;
      position: relative;
    }

    .tf-choice:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.5);
      transform: scale(1.02);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .tf-choice-key {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      border: 1px solid rgba(255, 255, 255, 0.4);
      border-radius: 6px;
      font-size: 12px;
      font-weight: 800;
      background: rgba(255, 255, 255, 0.05);
      transition: all 0.2s ease;
    }

    .tf-choice:hover .tf-choice-key {
      background: white;
      color: #0d47a1;
      border-color: white;
      transform: scale(1.1);
    }

    .tf-choice-text {
      font-size: 18px;
      font-weight: 600;
      letter-spacing: -0.2px;
    }

    .tf-choice i {
      font-size: 20px;
      margin-left: auto;
      opacity: 0.9;
      color: #bbdefb;
    }

    .tf-back-btn {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.7);
      padding: 8px 0;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      width: fit-content;
      margin-top: 10px;
    }

    .tf-back-btn:hover {
      color: white;
    }

    @keyframes tf-fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (min-width: 768px) {
      .tf-question {
        font-size: 32px;
      }

      .tf-choice {
        padding: 18px 24px;
      }

      .tf-choice-text {
        font-size: 19px;
      }
    }

    <style>@view-transition {
      navigation: auto;
    }
  </style>

  <script>
    // Prioritized background loading
    const backgroundImages = [
      'https://balidiving.com/images/thumbnails/1-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/10-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/11-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/12-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/13-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/14-bali-diving.jpg',
      'https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg', 'https://balidiving.com/images/thumbnails/2-bali-diving.jpg',
      'https://balidiving.com/images/thumbnails/20-bali-diving.jpg', 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg', 'https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg', 'https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg',
      'https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg', 'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg'
    ];
    const randomImage = backgroundImages[Math.floor(Math.random() * backgroundImages.length)];
    const preloadImg = new Image(); preloadImg.src = randomImage;
    document.write(`<style>.bio-wrapper::before { background-image: url('${randomImage}'); }</style>`);
  </script>
  <script src="/_sdk/data_sdk.js" type="text/javascript" defer></script>
  <script src="https://cdn.tailwindcss.com" type="text/javascript" defer></script>
  <!-- Meta Pixel Code -->
  <script>
    !function (f, b, e, v, n, t, s) {
      if (f.fbq) return; n = f.fbq = function () {
        n.callMethod ?
          n.callMethod.apply(n, arguments) : n.queue.push(arguments)
      };
      if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
      n.queue = []; t = b.createElement(e); t.async = !0;
      t.src = v; s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
      'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '2151240455197949');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=2151240455197949&ev=PageView&noscript=1" /></noscript>
  <!-- End Meta Pixel Code -->
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17535474834"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'AW-17535474834');
  </script>
</head>

<body>
  <main class="bio-wrapper">
    <div class="bio-content">
      <header class="profile-header">
        <div class="avatar-circle"><img src="https://balidiving.com/logo-balidiving-250.jpg" alt="Bali Diving Logo"
            onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-water\'></i>';">
        </div>
        <h1 class="company-title" id="companyTitle">BALI DIVING</h1>
        <h2 class="business-name" id="businessName">Bali's Most Famous Dive Centre</h2>
        <p class="business-tagline" id="businessTagline">🌐 PADI - 5 Star Dive Centre</p>
        <div class="business-details">
          <div class="detail-row">
            <i class="fas fa-map-marker-alt"></i>
            <a href="https://maps.app.goo.gl/xir2mavdKKixnuYp9" target="_blank" rel="noopener noreferrer"
              id="businessLocation" class="location-link">Find on Map</a>
          </div>
          <div class="detail-row"><i class="fas fa-envelope"></i> <span id="businessEmail"><a
                href="https://mail.google.com/mail/?view=cm&fs=1&to=sales@balidiving.com&su=KONTEN PROMO&body=Isi Iklan:&bcc=admin@balidiving.com"
                target="_blank">sales@balidiving.com</a></span>
          </div>
          <div class="detail-row"><i class="fab fa-whatsapp"></i> <span id="businessWhatsapp"><a
                href="https://wa.me/6287861190174" target="_blank">+6287861190174</a></span>
          </div>
        </div>
      </header>

      <!-- Tab Content -->
      <div class="tab-content-wrapper">
        <!-- Quick Links Tab -->
        <div id="quickContent" class="tab-content active">
          <div class="tf-container">
            <div class="tf-progress-bar">
              <div class="tf-progress-value" id="tf-progress"></div>
            </div>
            <!-- STEP 1: Main Selection -->
            <div class="tf-step active" id="tf-step-1">
              <h2 class="tf-question">Which blue adventure are you dreaming of today? 🌊</h2>
              <div class="tf-choices">
                <div class="tf-choice" onclick="tfNextStep(2)">
                  <span class="tf-choice-key">A</span>
                  <div class="flex flex-col">
                    <span class="tf-choice-text">Snorkeling</span>
                    <small style="font-size: 11px; opacity: 0.7;">Float above the coral gardens</small>
                  </div>
                  <i class="fas fa-mask"></i>
                </div>
                <div class="tf-choice" onclick="tfNextStep(3)">
                  <span class="tf-choice-key">B</span>
                  <div class="flex flex-col">
                    <span class="tf-choice-text">Try Scuba Diving</span>
                    <small style="font-size: 11px; opacity: 0.7;">Take your first breath underwater</small>
                  </div>
                  <i class="fas fa-swimmer"></i>
                </div>
                <div class="tf-choice" onclick="tfNextStep(4)">
                  <span class="tf-choice-key">C</span>
                  <div class="flex flex-col">
                    <span class="tf-choice-text">Go Diving</span>
                    <small style="font-size: 11px; opacity: 0.7;">For certified divers exploring Bali</small>
                  </div>
                  <i class="fas fa-fish"></i>
                </div>
                <div class="tf-choice" onclick="tfNextStep(5)">
                  <span class="tf-choice-key">D</span>
                  <div class="flex flex-col">
                    <span class="tf-choice-text">PADI Certification</span>
                    <small style="font-size: 11px; opacity: 0.7;">Get licensed and dive the world</small>
                  </div>
                  <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="tf-choice" id="planning-support-choice" onclick="tfNextStep(7)"
                  style="background: rgba(255, 255, 255, 0.05); border-style: dashed;">
                  <span class="tf-choice-key">E</span>
                  <span class="tf-choice-text">Planning & Support</span>
                  <i class="fas fa-info-circle"></i>
                </div>
              </div>
            </div>

            <!-- STEP 2: Snorkeling Sub-Menu -->
            <div class="tf-step" id="tf-step-2">
              <h2 class="tf-question">Everything about Snorkeling. 🤿</h2>
              <div class="tf-choices">
                <a href="https://www.balidiving.com/snorkeling" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Snorkeling Packages</span>
                  <i class="fas fa-box-open"></i>
                </a>
                <a href="https://balidiving.com/pricelist" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">Show Price List</span>
                  <i class="fas fa-tags"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">C</span>
                  <span class="tf-choice-text">Send Private Message</span>
                  <i class="fas fa-envelope"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Change activity
                </button>
              </div>
            </div>

            <!-- STEP 3: Try Diving Sub-Menu -->
            <div class="tf-step" id="tf-step-3">
              <h2 class="tf-question">Experience your first dive. 🌊</h2>
              <div class="tf-choices">
                <a href="https://balidiving.com/try-scuba-diving" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Try Diving Packages</span>
                  <i class="fas fa-box-open"></i>
                </a>
                <a href="https://balidiving.com/pricelist" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">Show Price List</span>
                  <i class="fas fa-tags"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">C</span>
                  <span class="tf-choice-text">Send Private Message</span>
                  <i class="fas fa-envelope"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Change activity
                </button>
              </div>
            </div>

            <!-- STEP 4: Go Diving Sub-Menu -->
            <div class="tf-step" id="tf-step-4">
              <h2 class="tf-question">Ready for a deeper dive? 🦈</h2>
              <div class="tf-choices">
                <a href="https://www.balidiving.com/discover-scuba-diving-in-bali" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Go Diving Packages</span>
                  <i class="fas fa-box-open"></i>
                </a>
                <a href="https://balidiving.com/pricelist" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">Show Price List</span>
                  <i class="fas fa-tags"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">C</span>
                  <span class="tf-choice-text">Send Private Message</span>
                  <i class="fas fa-envelope"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Change activity
                </button>
              </div>
            </div>

            <!-- STEP 5: PADI Certification Sub-Menu -->
            <div class="tf-step" id="tf-step-5">
              <h2 class="tf-question">Get your world-recognized license. 🎓</h2>
              <div class="tf-choices">
                <a href="https://www.balidiving.com/scuba-diving-certification" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">PADI Course Packages</span>
                  <i class="fas fa-box-open"></i>
                </a>
                <a href="https://balidiving.com/pricelist" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">Show Price List</span>
                  <i class="fas fa-tags"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">C</span>
                  <span class="tf-choice-text">Send Private Message</span>
                  <i class="fas fa-envelope"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Change activity
                </button>
              </div>
            </div>

            <!-- STEP 6: Integrated Contact Form -->
            <div class="tf-step" id="tf-step-6">
              <h2 class="tf-question">We're all ears. How can we help? 🐚</h2>
              <div class="email-form-container">
                <div class="department-selector">
                  <label for="departmentSelect">
                    <i class="fas fa-building"></i> Select Department
                  </label>
                  <select id="departmentSelect" onchange="updateEmailForm()">
                    <option value="sales@balidiving.com">Sales Team</option>
                    <option value="customer.service@balidiving.com">Customer Service</option>
                    <option value="subhi@balidiving.com">IT Support</option>
                    <option value="marketing@balidiving.com">Marketing</option>
                    <option value="henry@balidiving.com">Operational</option>
                  </select>
                </div>

                <div id="emailFormFrame" class="email-form-frame">
                  <div class="email-form-content">
                    <form id="contactForm" class="contact-form" action="" method="POST">
                      <input type="hidden" name="_subject" id="formSubmitSubject"
                        value="New inquiry from Bali Diving Bio Page">
                      <input type="hidden" name="_template" value="table">
                      <input type="hidden" name="_captcha" value="false">
                      <input type="hidden" name="_next" value="https://balidiving.com/bio.php?sent=true">
                      <input type="text" name="_honey" style="display:none">
                      <input type="hidden" name="Department" id="hiddenDepartment" value="Sales Team">

                      <div class="form-group">
                        <label for="senderName"><i class="fas fa-user"></i> Your Name *</label>
                        <input type="text" id="senderName" name="Name" required placeholder="Enter your full name">
                      </div>

                      <div class="form-group">
                        <label for="senderEmail"><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" id="senderEmail" name="Email" required placeholder="your@email.com">
                      </div>

                      <div class="form-group">
                        <label for="senderPhone"><i class="fas fa-phone"></i> Phone Number *</label>
                        <input type="tel" id="senderPhone" name="Phone" required placeholder="+62 xxx xxxx xxxx">
                      </div>

                      <div class="form-group">
                        <label for="emailSubject"><i class="fas fa-tag"></i> Subject *</label>
                        <input type="text" id="emailSubject" name="Subject" required
                          placeholder="Subject of your inquiry">
                      </div>

                      <div class="form-group">
                        <label for="emailMessage"><i class="fas fa-comment-dots"></i> Message *</label>
                        <textarea id="emailMessage" name="Message" required rows="4"
                          placeholder="Type your message here..."></textarea>
                      </div>

                      <div class="form-actions">
                        <button type="submit" class="send-btn send-email">
                          <i class="fas fa-paper-plane"></i> Send Email
                        </button>
                        <button type="button" class="send-btn send-whatsapp" onclick="sendViaWhatsApp(event)">
                          <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)" style="color: white; margin-top: 20px;">
                  <i class="fas fa-arrow-left"></i> Back to main menu
                </button>
              </div>
            </div>

            <!-- STEP 7: Prices, Weather & Planning -->
            <div class="tf-step" id="tf-step-7">
              <h2 class="tf-question">Let's get the details right. What do you need? 📋</h2>
              <div class="tf-choices">
                <a href="https://balidiving.com/pricelist" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Show me the Price List</span>
                  <i class="fas fa-tags"></i>
                </a>
                <a href="https://balidiving.com/weather" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">How's the water today? (Weather)</span>
                  <i class="fas fa-sun"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(9)">
                  <span class="tf-choice-key">C</span>
                  <span class="tf-choice-text">Our Reviews & Social</span>
                  <i class="fas fa-camera"></i>
                </div>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">D</span>
                  <span class="tf-choice-text">Speak with our Team</span>
                  <i class="fas fa-comments"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Back to adventures
                </button>
              </div>
            </div>

            <!-- STEP 8: About -->
            <div class="tf-step" id="tf-step-8">
              <h2 class="tf-question">Why Bali Diving? 🤿</h2>
              <div class="tf-choices">
                <a href="https://balidiving.com/about-us" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Our Story & PADI 5-Star Status</span>
                  <i class="fas fa-award"></i>
                </a>
                <button class="tf-back-btn" onclick="tfNextStep(1)">
                  <i class="fas fa-arrow-left"></i> Back to start
                </button>
              </div>
            </div>

            <!-- STEP 9: Social & Reviews -->
            <div class="tf-step" id="tf-step-9">
              <h2 class="tf-question">Love underwater stories? Let's connect! 📸</h2>
              <div class="tf-choices">
                <a href="https://g.page/r/CSRkANeDMYbaEAE/review" target="_blank" class="tf-choice">
                  <span class="tf-choice-key">A</span>
                  <span class="tf-choice-text">Share your diving experience (Review)</span>
                  <i class="fas fa-star"></i>
                </a>
                <div class="tf-choice" onclick="tfNextStep(6)">
                  <span class="tf-choice-key">B</span>
                  <span class="tf-choice-text">Send us a private message</span>
                  <i class="fas fa-envelope"></i>
                </div>
                <button class="tf-back-btn" onclick="tfNextStep(7)">
                  <i class="fas fa-arrow-left"></i> Back
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="social-media">
        <a href="https://www.instagram.com/bali_diving/" target="_blank" rel="noopener noreferrer"
          class="social-icon instagram" aria-label="Instagram">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="https://www.facebook.com/balidivingsunfish" target="_blank" rel="noopener noreferrer"
          class="social-icon facebook" aria-label="Facebook">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.tiktok.com/@balidiving" target="_blank" rel="noopener noreferrer"
          class="social-icon tiktok" aria-label="TikTok">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="https://wa.me/6287861190174?text=Hello%20Bali%20Diving!%20I%20discovered%20your%20contact%20through%20your%20website.%20I'm%20interested%20in%20learning%20more%20about%20your%20diving%20services."
          target="_blank" rel="noopener noreferrer" class="social-icon whatsapp" aria-label="WhatsApp">
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>
  </main>
  <script>
    // Navigation and Typeform Logic

    // Simplified Navigation
    function switchTab(tabName) {
      tfNextStep(1); // Reset to first step
    }

    // Typeform Step Logic
    function tfNextStep(stepNumber) {
      const steps = document.querySelectorAll('.tf-step');
      steps.forEach(step => step.classList.remove('active'));

      const nextStep = document.getElementById(`tf-step-${stepNumber}`);
      if (nextStep) {
        nextStep.classList.add('active');
        // Update Progress Bar (assuming 9 total steps)
        const progress = (stepNumber / 9) * 100;
        document.getElementById('tf-progress').style.width = `${progress}%`;
      }
    }

    // Keyboard Shortcuts Support
    document.addEventListener('keydown', function (event) {
      const activeStep = document.querySelector('.tf-step.active');
      if (!activeStep) return;

      // Ignore if user is typing in form fields
      if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;

      const key = event.key.toUpperCase();
      const choiceMap = { 'A': 0, 'B': 1, 'C': 2, 'D': 3, 'E': 4 };

      if (choiceMap.hasOwnProperty(key)) {
        const choices = activeStep.querySelectorAll('.tf-choice');
        const choiceIndex = choiceMap[key];
        if (choices[choiceIndex]) {
          choices[choiceIndex].click();
        }
      }

      // 'Enter' on first choice if applicable
      if (event.key === 'Enter') {
        const firstChoice = activeStep.querySelector('.tf-choice');
        if (firstChoice) firstChoice.click();
      }
    });

    // Update Email Form based on Department Selection
    function updateEmailForm() {
      const departmentSelect = document.getElementById('departmentSelect');
      const selectedEmail = departmentSelect.value;
      const selectedDepartment = departmentSelect.options[departmentSelect.selectedIndex].text;

      // Update UI to show selected department
      document.getElementById('deptDisplay').textContent = selectedDepartment;
      document.getElementById('hiddenDepartment').value = selectedDepartment;

      // Set FormSubmit action URL with selected email
      const form = document.getElementById('contactForm');
      form.action = `https://formsubmit.co/${selectedEmail}`;

      // Update subject field
      document.getElementById('formSubmitSubject').value = `New inquiry from Bali Diving Bio Page - ${selectedDepartment}`;

      // Store selected email in a data attribute for WhatsApp
      departmentSelect.setAttribute('data-selected-email', selectedEmail);
      departmentSelect.setAttribute('data-selected-dept', selectedDepartment);

      console.log('Selected Department:', selectedDepartment);
      console.log('Email:', selectedEmail);
      console.log('FormSubmit Action:', form.action);
    }

    // Check if form was successfully submitted
    function checkSubmissionStatus() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('sent') === 'true') {
        // Switch to the step that has the contact form or final message
        tfNextStep(5);

        // Show success message
        setTimeout(() => {
          alert('✅ Your message has been sent successfully! We will get back to you soon.');

          // Remove the query parameter from URL
          window.history.replaceState({}, document.title, window.location.pathname);
        }, 500);
      }
    }


    // Validate Form
    function validateForm() {
      const name = document.getElementById('senderName').value.trim();
      const email = document.getElementById('senderEmail').value.trim();
      const phone = document.getElementById('senderPhone').value.trim();
      const subject = document.getElementById('emailSubject').value.trim();
      const message = document.getElementById('emailMessage').value.trim();

      if (!name || !email || !phone || !subject || !message) {
        alert('Please fill in all required fields!');
        return false;
      }

      // Basic email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Please enter a valid email address!');
        return false;
      }

      return true;
    }

    // Send via WhatsApp
    function sendViaWhatsApp(event) {
      event.preventDefault();

      if (!validateForm()) {
        return;
      }

      const departmentSelect = document.getElementById('departmentSelect');
      const deptName = departmentSelect.getAttribute('data-selected-dept');

      const name = document.getElementById('senderName').value.trim();
      const email = document.getElementById('senderEmail').value.trim();
      const phone = document.getElementById('senderPhone').value.trim();
      const subject = document.getElementById('emailSubject').value.trim();
      const message = document.getElementById('emailMessage').value.trim();

      // Compose WhatsApp message
      const waMessage = `*${subject}*\n\n` +
        `*Name:* ${name}\n` +
        `*Email:* ${email}\n` +
        `*Phone:* ${phone}\n\n` +
        `*Message:*\n${message}\n\n` +
        `_Sent to: ${deptName}_`;

      // WhatsApp number (Bali Diving)
      const waNumber = '6287861190174';
      const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`;

      // Open WhatsApp in new tab
      window.open(waUrl, '_blank');

      // Optional: Show success message
      alert('Opening WhatsApp...');
    }

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', function () {
      updateEmailForm();
      checkSubmissionStatus();

      // Idle Redirect Logic (1 minute)
      let idleTimer;
      const idleLimit = 60000; // 60 seconds
      const redirectUrl = 'https://balidiving.com/pricelist';

      function resetIdleTimer() {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => {
          window.location.href = redirectUrl;
        }, idleLimit);
      }

      // Events that reset the timer (Click & Typing only)
      const activityEvents = ['click', 'mousedown', 'keydown', 'input', 'touchstart'];

      activityEvents.forEach(event => {
        document.addEventListener(event, resetIdleTimer, true);
      });

      // Start the timer initially
      resetIdleTimer();

      // ==========================================
      // LOCAL FIRST: FORM DATA PERSISTENCE
      // ==========================================
      const formFields = ['senderName', 'senderEmail', 'senderPhone', 'emailSubject', 'emailMessage'];

      // Load saved data
      formFields.forEach(id => {
        const savedValue = localStorage.getItem('bali_diving_form_' + id);
        if (savedValue) {
          const field = document.getElementById(id);
          if (field) field.value = savedValue;
        }
      });

      // Save data on input
      formFields.forEach(id => {
        const field = document.getElementById(id);
        if (field) {
          field.addEventListener('input', (e) => {
            localStorage.setItem('bali_diving_form_' + id, e.target.value);
          });
        }
      });

      // Clear data on successful submission (optional - maybe keep for user convenience?)
      // We'll keep it for now as "draft" behavior is often preferred.

      // ==========================================
      // LOCAL FIRST: SERVICE WORKER REGISTRATION
      // ==========================================
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          // Use relative path - safer if file structure changes
          navigator.serviceWorker.register('./service-worker.js')
            .then(registration => {
              console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, err => {
              console.log('ServiceWorker registration failed: ', err);
            });
        });
      }

      /* Offline visual indicator removed per request */
    });




    const defaultConfig = {
      company_title: "BALI DIVING",
      profile_name: "Bali's Most Famous Dive Centre",
      tagline: "🌐 PADI - 5 Star Dive Centre",
      location: "📍 Find on Map",
      email: "sales@balidiving.com",
      whatsapp: "+6287861190174",
      link1_text: "Website",
      link2_text: "Booking Now",
      link3_text: "Price List",
      link4_text: "Review",
      link5_text: "About Bali Diving",
      link6_text: "Recommendations",
      link7_text: "F.A.Q.",
      background_color: "#0a74da",
      button_color: "#ffffff",
      text_color: "#1a1a1a",
      font_family: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif",
      font_size: 16,
      link99_text: "Today Weather"
    };

    async function onConfigChange(config) {
      const baseFontSize = config.font_size || defaultConfig.font_size;
      const customFont = config.font_family || defaultConfig.font_family;
      const baseFontStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif';

      document.body.style.fontFamily = `${customFont}, ${baseFontStack}`;

      document.getElementById('companyTitle').textContent = config.company_title || defaultConfig.company_title;
      document.getElementById('companyTitle').style.fontSize = `${baseFontSize * 2.25}px`;

      document.getElementById('businessName').textContent = config.profile_name || defaultConfig.profile_name;
      document.getElementById('businessName').style.fontSize = `${baseFontSize * 1.375}px`;

      document.getElementById('businessTagline').textContent = config.tagline || defaultConfig.tagline;
      document.getElementById('businessTagline').style.fontSize = `${baseFontSize}px`;

      document.getElementById('businessLocation').textContent = config.location || defaultConfig.location;
      document.getElementById('businessEmail').textContent = config.email || defaultConfig.email;
      document.getElementById('businessWhatsapp').textContent = config.whatsapp || defaultConfig.whatsapp;

      const detailRows = document.querySelectorAll('.detail-row');
      detailRows.forEach(row => {
        row.style.fontSize = `${baseFontSize * 0.9375}px`;
      });

      document.getElementById('linkText1').textContent = config.link1_text || defaultConfig.link1_text;
      document.getElementById('linkText2').textContent = config.link2_text || defaultConfig.link2_text;
      document.getElementById('linkText3').textContent = config.link3_text || defaultConfig.link3_text;
      document.getElementById('linkText4').textContent = config.link4_text || defaultConfig.link4_text;
      document.getElementById('linkText5').textContent = config.link5_text || defaultConfig.link5_text;
      document.getElementById('linkText6').textContent = config.link6_text || defaultConfig.link6_text;
      document.getElementById('linkText7').textContent = config.link7_text || defaultConfig.link7_text;
      document.getElementById('linkText99').textContent = config.link99_text || defaultConfig.link99_text;
      const actionLinks = document.querySelectorAll('.action-link');
      actionLinks.forEach(link => {
        link.style.fontSize = `${baseFontSize * 1.0625}px`;
      });

      const bgColor = config.background_color || defaultConfig.background_color;
      const wrapper = document.querySelector('.bio-wrapper');
      const darkerBg = adjustColorBrightness(bgColor, -15);
      wrapper.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${darkerBg} 100%)`;

      const buttonColor = config.button_color || defaultConfig.button_color;
      actionLinks.forEach(link => {
        link.style.backgroundColor = buttonColor;
      });

      const textColor = config.text_color || defaultConfig.text_color;
      actionLinks.forEach(link => {
        link.style.color = textColor;
      });
    }

    function adjustColorBrightness(hex, percent) {
      const num = parseInt(hex.replace('#', ''), 16);
      const amt = Math.round(2.55 * percent);
      const R = Math.max(0, Math.min(255, (num >> 16) + amt));
      const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
      const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
      return '#' + ((1 << 24) + (R << 16) + (G << 8) + B).toString(16).slice(1);
    }

    function mapToCapabilities(config) {
      return {
        recolorables: [
          {
            get: () => config.background_color || defaultConfig.background_color,
            set: (value) => {
              config.background_color = value;
              window.elementSdk.setConfig({ background_color: value });
            }
          },
          {
            get: () => config.button_color || defaultConfig.button_color,
            set: (value) => {
              config.button_color = value;
              window.elementSdk.setConfig({ button_color: value });
            }
          },
          {
            get: () => config.text_color || defaultConfig.text_color,
            set: (value) => {
              config.text_color = value;
              window.elementSdk.setConfig({ text_color: value });
            }
          }
        ],
        borderables: [],
        fontEditable: {
          get: () => config.font_family || defaultConfig.font_family,
          set: (value) => {
            config.font_family = value;
            window.elementSdk.setConfig({ font_family: value });
          }
        },
        fontSizeable: {
          get: () => config.font_size || defaultConfig.font_size,
          set: (value) => {
            config.font_size = value;
            window.elementSdk.setConfig({ font_size: value });
          }
        }
      };
    }

    function mapToEditPanelValues(config) {
      return new Map([
        ["company_title", config.company_title || defaultConfig.company_title],
        ["profile_name", config.profile_name || defaultConfig.profile_name],
        ["tagline", config.tagline || defaultConfig.tagline],
        ["location", config.location || defaultConfig.location],
        ["email", config.email || defaultConfig.email],
        ["whatsapp", config.whatsapp || defaultConfig.whatsapp],
        ["link1_text", config.link1_text || defaultConfig.link1_text],
        ["link2_text", config.link2_text || defaultConfig.link2_text],
        ["link3_text", config.link3_text || defaultConfig.link3_text],
        ["link4_text", config.link4_text || defaultConfig.link4_text],
        ["link5_text", config.link5_text || defaultConfig.link5_text],
        ["link6_text", config.link6_text || defaultConfig.link6_text],
        ["link7_text", config.link7_text || defaultConfig.link7_text],
        ["link99_text", config.link99_text || defaultConfig.link99_text]
      ]);
    }

    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities,
        mapToEditPanelValues
      });
    }
  </script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9a71f2df24193e4a',t:'MTc2NDU4NTQ0MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "TouristAttraction",
    "name": "Bali Diving - PADI 5 Star Dive Centre",
    "description": "Bali's Most Famous Dive Centre based in Sanur. Offers PADI Courses, Fun Dives, and Snorkeling.",
    "url": "https://balidiving.com",
    "image": "https://balidiving.com/logo-balidiving-250.jpg",
    "priceRange": "$$",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Sanur",
      "addressRegion": "Bali",
      "addressCountry": "ID"
    }
  }
  </script>
</body>

</html>