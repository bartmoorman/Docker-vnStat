FROM bmoorman/ubuntu:focal AS builder

ARG DEBIAN_FRONTEND=noninteractive

WORKDIR /opt/vnstat

RUN apt-get update \
 && apt-get install --yes --no-install-recommends \
    build-essential \
    curl \
    libsqlite3-dev \
 && curl --silent --location "https://humdi.net/vnstat/vnstat-latest.tar.gz" | tar xz --strip-components 1 \
 && ./configure && make

FROM bmoorman/ubuntu:focal

ARG DEBIAN_FRONTEND=noninteractive

ENV HTTPD_SERVERNAME=localhost \
    HTTPD_PORT=1477

RUN apt-get update \
 && apt-get install --yes --no-install-recommends \
    apache2 \
    curl \
    libapache2-mod-php \
    php-sqlite3 \
    ssl-cert \
    wget \
 && a2enmod \
    remoteip \
    rewrite \
    ssl \
 && sed --in-place --regexp-extended \
    --expression 's|^(Include\s+ports\.conf)$|#\1|' \
    /etc/apache2/apache2.conf \
 && wget --quiet --directory-prefix /usr/local/bin "https://dl.eff.org/certbot-auto" \
 && chmod +x /usr/local/bin/certbot-auto \
 && apt-get autoremove --yes --purge \
 && apt-get clean \
 && rm --recursive --force /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=builder /opt/vnstat/vnstat* /usr/sbin/
COPY apache2/ /etc/apache2/
COPY htdocs/ /var/www/html/

VOLUME /config /var/lib/vnstat

EXPOSE ${HTTPD_PORT}

CMD ["/etc/apache2/start.sh"]

HEALTHCHECK --interval=60s --timeout=5s CMD /etc/apache2/healthcheck.sh || exit 1
