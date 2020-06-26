### Docker Run
```
docker run \
--detach \
--name vnstat \
--network host \
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
    volumes:
      - vnstat-config:/config
      - vnstat-data:/var/lib/vnstat

volumes:
  vnstat-config:
  vnstat-data:
```

### Environment Variables
|Variable|Description|Default|
|--------|-----------|-------|
|TZ|Sets the timezone|`America/Denver`|
|HTTPD_SERVERNAME|Sets the vhost servername|`localhost`|
|HTTPD_PORTSets the vhost port|`1477`|
|HTTPD_SSL|Set to anything other than `SSL` (e.g. `NO_SSL`) to disable SSL|`SSL`|
|HTTPD_REDIRECT|Set to anything other than `REDIRECT` (e.g. `NO_REDIRECT`) to disable SSL redirect|`REDIRECT`|
