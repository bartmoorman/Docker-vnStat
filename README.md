### Usage
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

