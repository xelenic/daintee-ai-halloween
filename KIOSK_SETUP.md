# Halloween Dracula Kiosk Setup

## Overview
This is a 6-step kiosk application for Halloween promotion that transforms users into Dracula using Gemini AI API.

## Features
- **Step 1**: Welcome screen with instructions
- **Step 2**: Phone number collection
- **Step 3**: Camera capture
- **Step 4**: Photo preview with retake option
- **Step 5**: AI processing (loading screen)
- **Step 6**: Final result with sharing options

## Screen Specifications
- **Ratio**: 375.688 x 699 pixels (kiosk screen)
- **Responsive design** with Halloween theme
- **Touch-friendly** interface

## Setup Instructions

### 1. Environment Configuration
Create a `.env` file in the root directory with the following variables:

```env
APP_NAME="Halloween Dracula Kiosk"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=http://your-domain.com

GEMINI_API_KEY=your_gemini_api_key_here

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 2. Database Setup
```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 3. Storage Setup
```bash
# Create storage link for public access
php artisan storage:link

# Create necessary directories (already done)
mkdir -p storage/app/public/kiosk/original
mkdir -p storage/app/public/processed
```

### 4. Gemini API Setup
1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key
3. Add it to your `.env` file as `GEMINI_API_KEY`
4. **Note**: This uses the Gemini 2.5 Flash Image Preview model for image generation

### 5. Start the Application
```bash
# Start the development server
php artisan serve

# Or for production
php artisan serve --host=0.0.0.0 --port=8000
```

## Access the Kiosk
Navigate to: `http://your-domain.com/kiosk`

## File Structure
```
app/
├── Http/Controllers/KioskController.php
├── Models/KioskSession.php
└── Services/GeminiService.php

resources/views/kiosk/
├── layout.blade.php
├── welcome.blade.php
├── phone-input.blade.php
├── camera.blade.php
├── preview.blade.php
├── processing.blade.php
└── result.blade.php

database/migrations/
└── create_kiosk_sessions_table.php
```

## Database Schema
The `kiosk_sessions` table stores:
- `session_id` (UUID)
- `phone_number`
- `original_image_path`
- `processed_image_path`
- `status` (enum: started, phone_collected, photo_captured, photo_confirmed, processing, completed, failed)
- `gemini_response` (JSON)
- `completed_at` (timestamp)

## API Endpoints
- `GET /kiosk` - Welcome screen
- `GET /kiosk/phone` - Phone input
- `POST /kiosk/phone` - Process phone number
- `GET /kiosk/camera/{sessionId}` - Camera capture
- `POST /kiosk/camera/{sessionId}` - Process photo
- `GET /kiosk/preview/{sessionId}` - Photo preview
- `POST /kiosk/confirm/{sessionId}` - Confirm photo
- `GET /kiosk/processing/{sessionId}` - Processing screen
- `GET /kiosk/status/{sessionId}` - Check processing status
- `GET /kiosk/result/{sessionId}` - Final result
- `POST /kiosk/retake/{sessionId}` - Retake photo
- `GET /kiosk/new` - Start new session

## Customization
- Modify the Gemini prompt in `app/Services/GeminiService.php`
- Update styling in `resources/views/kiosk/layout.blade.php`
- Add additional image processing in the `GeminiService`
- The service uses the `gemini-2.5-flash-image-preview` model for image generation

## Troubleshooting
1. **Camera not working**: Ensure HTTPS or localhost for camera access
2. **Gemini API errors**: Check API key and quota
3. **Image upload issues**: Verify storage permissions
4. **Database errors**: Run `php artisan migrate:fresh`

## Production Deployment
1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Use a proper web server (Apache/Nginx)
3. Configure SSL for camera access
4. Set up proper file permissions
5. Configure database backup

## Security Notes
- Phone numbers are stored for the session only
- Images are stored temporarily
- Consider implementing session cleanup
- Add rate limiting for production use
