# Google Maps Lead Scraper

A unified, containerized web application for automated lead generation and scraping from Google Maps. The application consists of a Laravel 11 dashboard/job manager and a Python-Selenium search scraper.

---

## 📂 Repository Structure

```text
google-maps-lead-scraper/
│
├── .github/
│   └── workflows/
│       └── deploy.yml      # CI/CD pipeline (Testing, Docker build & push, VPS deploy)
│
├── laravel-app/            # Laravel web application (dashboard, queue worker, lead manager)
│
├── scraper/                # Python Selenium scraping scraper
│
├── Dockerfile              # Unified multi-service production Dockerfile
│
├── requirements.txt        # Python scraper dependencies
│
└── README.md               # Main repository documentation
```

---

## 🛠️ Components Overview

### 1. Web Dashboard (`laravel-app/`)
* **Framework**: Laravel 11 / PHP 8.2
* **Database**: MySQL / MariaDB (handles search history, leads storage, user sessions)
* **Job Queue**: Laravel Queue Worker (processes scraping runs in the background)
* **Features**:
  * Real-time search progress updates
  * Excel/CSV exports for scraped leads
  * Search history management (resume, pause, stop, delete)
  * Google OAuth and simple profile management

### 2. Google Maps Scraper (`scraper/`)
* **Language**: Python 3.10+
* **Frameworks**: Selenium, Chromium, Webdriver-Manager
* **Function**: Runs headless inside the container, parses Google Maps search results, extracts lead details (names, ratings, websites, phone numbers, addresses), and feeds them back to the database.

---

## 🚀 Getting Started

### Prerequisites
* Docker & Docker Compose
* *OR* local PHP 8.2+, Composer, Node.js, and Python 3.10+

### 🐳 Running with Docker (Recommended)

1. Clone the repository and navigate to the directory:
   ```bash
   git clone https://github.com/sae2919/google-maps-lead-scraper.git
   cd google-maps-lead-scraper
   ```

2. Build and run the unified container:
   ```bash
   docker build -t google-maps-lead-scraper .
   docker run -d -p 8080:80 --name google-maps-lead-scraper google-maps-lead-scraper
   ```

3. Open your browser to `http://localhost:8080` to access the dashboard.

---

## 🚀 CI/CD & Automated Deployment

The project includes a GitHub Actions workflow in [deploy.yml](file:///.github/workflows/deploy.yml) that automatically runs on commits pushed to `main`.

### Workflow Stages
1. **Test & Lint**: Runs code quality tools for Laravel PHP and Python Selenium scripts.
2. **Build & Push Docker Image**: Packages the entire application using the root `Dockerfile` and publishes the image to **GitHub Container Registry (GHCR)** at `ghcr.io/sae2919/google-maps-lead-scraper:latest`.
3. **Deploy to VPS**: Uses SSH to log into your deployment server, pulls the fresh image, and restarts the web/worker container.

### Deploy Setup
To enable automated deployment to your VPS, add the following secrets in your GitHub repository settings under **Settings > Secrets and variables > Actions**:

| Secret Key | Description |
| :--- | :--- |
| `SSH_HOST` | The IP address or domain name of your VPS |
| `SSH_USER` | The SSH username (e.g., `root`, `ubuntu`) |
| `SSH_KEY` | The private SSH key used to log in |
| `SSH_PORT` | (Optional) Custom SSH port (defaults to `22`) |

---

## 📝 License
This project is open-sourced and licensed under the [MIT license](https://opensource.org/licenses/MIT).
