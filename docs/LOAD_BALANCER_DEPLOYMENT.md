# Load Balancer Deployment Guide for Steman Alumni

## Overview
This guide provides a comprehensive long-term solution to prevent nginx SSL certificate errors (521, 526, 500) and implements a load balancer setup for high availability.

## Architecture

### Current Setup
- Single nginx container handling SSL termination
- Single app container
- Manual SSL certificate management

### New Setup with Load Balancer
- HAProxy as load balancer with SSL termination
- Multiple app instances (3) for load balancing
- Automated SSL certificate management
- Health checks and monitoring
- Automatic failover

## Components

### 1. HAProxy Load Balancer
- **Purpose**: Distribute traffic across multiple app instances
- **Features**:
  - SSL termination
  - Round-robin load balancing
  - Health checks
  - Session persistence (cookies)
  - Statistics dashboard (port 8404)

### 2. SSL Certificate Automation
- **Script**: `scripts/ssl_automation.sh`
- **Features**:
  - Automatic certificate renewal
  - Certificate validation
  - Backup before renewal
  - Nginx reload after renewal
  - Notifications (webhook/Telegram)

### 3. Health Check System
- **Script**: `scripts/health_check.sh`
- **Features**:
  - Container health monitoring
  - Website accessibility checks
  - SSL certificate expiry monitoring
  - Disk and memory monitoring
  - Automatic container restart
  - Notifications

### 4. Error Monitoring
- **Script**: `scripts/monitor_errors.sh`
- **Features**:
  - Laravel log monitoring
  - Emergency log monitoring
  - Error threshold alerts
  - Telegram notifications

### 5. Automated Backup
- **Script**: `scripts/backup_database.sh`
- **Features**:
  - Daily database backups
  - Compression
  - Automatic cleanup (7-day retention)
  - Notifications

## Deployment Steps

### Step 1: Prepare Server
```bash
# SSH to server
ssh root@103.175.219.57

# Create necessary directories
mkdir -p /var/www/steman-alumni/docker/haproxy
mkdir -p /var/www/steman-alumni/scripts
mkdir -p /var/www/steman-alumni/logs
mkdir -p /var/www/ssl-backups
```

### Step 2: Copy Files to Server
```bash
# Copy from local to server
pscp -pw Ch4v4run3@ docker-compose.lb.yml root@103.175.219.57:/var/www/steman-alumni/
pscp -pw Ch4v4run3@ docker/haproxy/haproxy.cfg root@103.175.219.57:/var/www/steman-alumni/docker/haproxy/
pscp -pw Ch4v4run3@ scripts/ssl_automation.sh root@103.175.219.57:/var/www/steman-alumni/scripts/
pscp -pw Ch4v4run3@ scripts/health_check.sh root@103.175.219.57:/var/www/steman-alumni/scripts/
```

### Step 3: Make Scripts Executable
```bash
ssh root@103.175.219.57
cd /var/www/steman-alumni
chmod +x scripts/*.sh
```

### Step 4: Stop Current Containers
```bash
cd /var/www/steman-alumni
docker-compose down
```

### Step 5: Start Load Balancer Setup
```bash
cd /var/www/steman-alumni
docker-compose -f docker-compose.lb.yml up -d
```

### Step 6: Setup Cron Jobs
```bash
cd /var/www/steman-alumni
./scripts/setup_cron.sh
```

### Step 7: Verify Setup
```bash
# Check HAProxy stats
curl http://103.175.219.57:8404
# Username: admin
# Password: StemanHAProxy2026!

# Check websites
curl -I https://alumni-steman.my.id
curl -I https://admin.alumni-steman.my.id

# Check container health
docker ps
docker exec steman_loadbalancer haproxy -c -f /usr/local/etc/haproxy/haproxy.cfg
```

## Configuration Details

### HAProxy Configuration
- **Stats**: http://103.175.219.57:8404
- **Backend Servers**: 3 app instances
- **Health Check**: HTTP GET /health
- **Load Balancing Algorithm**: Round-robin with session persistence
- **SSL**: Certificates from /etc/letsencrypt

### SSL Certificate Automation
- **Check Interval**: Daily at 1 AM
- **Renewal Threshold**: 30 days before expiry
- **Backup Location**: /var/www/ssl-backups
- **Backup Retention**: 7 days

### Health Check Schedule
- **Container Health**: Every 5 minutes
- **Website Check**: Every 5 minutes
- **SSL Check**: Every 5 minutes
- **System Resources**: Every 5 minutes

## Monitoring

### HAProxy Statistics Dashboard
- URL: http://103.175.219.57:8404
- Shows: Backend status, response times, request rates

### Grafana Dashboard
- URL: https://admin.alumni-steman.my.id/grafana/
- Shows: System metrics, container health, application metrics

### Log Files
- Health checks: `/var/www/steman-alumni/logs/health.log`
- SSL automation: `/var/www/steman-alumni/logs/ssl.log`
- Error monitoring: `/var/www/steman-alumni/logs/monitor.log`
- Backups: `/var/www/steman-alumni/logs/backup.log`

## Troubleshooting

### HAProxy Issues
```bash
# Check HAProxy logs
docker logs steman_loadbalancer

# Test HAProxy configuration
docker exec steman_loadbalancer haproxy -c -f /usr/local/etc/haproxy/haproxy.cfg

# Restart HAProxy
docker restart steman_loadbalancer
```

### SSL Certificate Issues
```bash
# Manually run SSL automation
cd /var/www/steman-alumni
./scripts/ssl_automation.sh

# Check certificate expiry
echo | openssl s_client -servername alumni-steman.my.id -connect alumni-steman.my.id:443 2>/dev/null | openssl x509 -noout -enddate

# Renew certificate manually
docker exec steman_certbot certbot renew --cert-name alumni-steman.my.id
```

### Container Health Issues
```bash
# Check all containers
docker ps

# Restart specific container
docker restart steman_app1

# Check container logs
docker logs steman_app1 --tail 100
```

## Rollback Procedure

If the load balancer setup has issues, rollback to the original setup:

```bash
cd /var/www/steman-alumni
docker-compose -f docker-compose.lb.yml down
docker-compose up -d
```

## Benefits

1. **High Availability**: Multiple app instances prevent single point of failure
2. **Load Distribution**: Traffic distributed across multiple instances
3. **SSL Automation**: Automatic certificate renewal prevents 521/526 errors
4. **Health Monitoring**: Proactive detection and resolution of issues
5. **Automatic Failover**: Unhealthy instances automatically removed from rotation
6. **Session Persistence**: User sessions maintained across requests
7. **Comprehensive Monitoring**: Full visibility into system health

## Maintenance

### Daily (Automated)
- SSL certificate check and renewal (1 AM)
- Database backup (2 AM)
- Application maintenance (3 AM)
- Health checks (every 5 minutes)
- Error monitoring (every 6 hours)

### Weekly (Automated)
- Storage cleanup (Sunday 4 AM)

### Monthly (Manual)
- Review logs
- Check backup retention
- Update SSL certificates if needed
- Review HAProxy statistics
- Update system packages

## Security Considerations

1. **HAProxy Stats**: Protected with username/password
2. **SSL Certificates**: Automated renewal with Let's Encrypt
3. **Container Isolation**: Each service in separate container
4. **Network Segmentation**: Docker bridge network
5. **Regular Updates**: Keep Docker images updated
6. **Backup Encryption**: Consider encrypting database backups

## Performance Tuning

### HAProxy
- Adjust `maxconn` based on server capacity
- Tune health check intervals
- Enable HTTP/2 for better performance

### Application
- Scale app instances based on load
- Optimize PHP-FPM settings
- Enable Redis caching
- Use CDN for static assets

## Contact

For issues or questions, contact the system administrator.
