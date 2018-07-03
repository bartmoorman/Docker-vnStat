FROM bmoorman/ubuntu:bionic AS builder

ARG DEBIAN_FRONTEND="noninteractive"

WORKDIR /opt

RUN apt-get update \
 && apt-get install --yes --no-install-recommends \
    build-essential \
    curl \
    libsqlite3-dev \
 && curl --silent --location "https://github.com/vergoh/vnstat/archive/master.tar.gz" | tar xz \
 && cd vnstat-master && ./configure && make

FROM bmoorman/ubuntu:bionic

ENV HTTPD_SERVERNAME="localhost"

ARG DEBIAN_FRONTEND="noninteractive"

RUN echo 'deb http://ppa.launchpad.net/certbot/certbot/ubuntu bionic main' > /etc/apt/sources.list.d/certbot.list \
 && echo 'deb-src http://ppa.launchpad.net/certbot/certbot/ubuntu bionic main' >> /etc/apt/sources.list.d/certbot.list \
 && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 75BCA694 \
 && apt-get update \
 && apt-get install --yes --no-install-recommends \
    apache2 \
    certbot \
    curl \
    libapache2-mod-php \
    php-sqlite3 \
    ssl-cert \
 && a2enmod \
    remoteip \
    rewrite \
    ssl \
 && sed --in-place --regexp-extended --expression '/^Listen\s+80/s/80/47001/' /etc/apache2/ports.conf \
 && sed --in-place --regexp-extended --expression '/<VirtualHost \*:80>/s/80/47001/' /etc/apache2/sites-enabled/000-default.conf \
 && apt-get autoremove --yes --purge \
 && apt-get clean \
 && rm --recursive --force /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=builder /opt/vnstat-master/vnstat /usr/bin
COPY --from=builder /opt/vnstat-master/vnstatd /usr/sbin
COPY apache2/ /etc/apache2/
COPY htdocs/ /var/www/html/

VOLUME /config /var/lib/vnstat

EXPOSE 1477

CMD ["/etc/apache2/start.sh"]

HEALTHCHECK --interval=60s --timeout=5s CMD curl --silent --location --fail http://localhost:47001/ > /dev/null || exit 1
