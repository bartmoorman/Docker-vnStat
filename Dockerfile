FROM bmoorman/ubuntu:focal

ARG DEBIAN_FRONTEND=noninteractive

ENV HTTPD_SERVERNAME=localhost \
    HTTPD_PORT=1477

RUN apt-get update \
 && apt-get install --yes --no-install-recommends \
    apache2 \
    certbot \
    curl \
    libapache2-mod-php \
    php-sqlite3 \
    ssl-cert \
    vnstat \
 && a2enmod \
    remoteip \
    rewrite \
    ssl \
 && a2dissite \
    000-default \
 && sed --in-place --regexp-extended \
    --expression 's|^(Include\s+ports\.conf)$|#\1|' \
    /etc/apache2/apache2.conf \
 && ln --symbolic --force /dev/stderr /var/log/apache2/error.log \
 && ln --symbolic --force /dev/stdout /var/log/apache2/access.log \
 && ln --symbolic --force /dev/stdout /var/log/apache2/other_vhosts_access.log \
 && apt-get autoremove --yes --purge \
 && apt-get clean \
 && rm --recursive --force /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY apache2/ /etc/apache2/
COPY htdocs/ /var/www/html/

VOLUME /config /var/lib/vnstat

EXPOSE ${HTTPD_PORT}

CMD ["/etc/apache2/start.sh"]

HEALTHCHECK --interval=60s --timeout=5s CMD /etc/apache2/healthcheck.sh || exit 1
