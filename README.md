### Docker Run
```
docker run \
--detach \
--name vnstat \
--network host \
--env "HTTPD_SERVERNAME=**sub.do.main**" \
--volume vnstat-config:/config \
--volume vnstat-data:/var/lib/vnstat \
bmoorman/vnstat:latest
```

### Docker Compose
```
version: "3.7"
services:
  vnstat:
    image: bmoorman/vnstat:latest
    container_name: vnstat
    network_mode: "host"
    environment:
      - HTTPD_SERVERNAME=**sub.do.main**
    volumes:
      - vnstat-config:/config
      - vnstat-data:/var/lib/vnstat

volumes:
  vnstat-config:
  vnstat-data:
```
