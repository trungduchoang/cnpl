FROM php:8.1-fpm
MAINTAINER Khanhnt <khanhnt@smartosc.com>

RUN apt-get update && apt-get install -y \
  curl \
  cron \
  git \
  gzip \
  libbz2-dev \
  libfreetype6-dev \
  libicu-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libpng-dev \
  libssh2-1-dev \
  libxslt1-dev \
  libzip-dev \
  lsof \
  default-mysql-client \
  vim \
  zip \
  gnupg2

RUN apt-get install -y \
  libcurl4-openssl-dev \
  libonig-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install \
  bcmath \
  ctype \
  curl \
  dom \
  gd \
  iconv \
  intl \
  mbstring \
  pdo_mysql \
  simplexml \
  soap \
  xsl \
  zip \
  sockets \
  bz2 \
  calendar \
  exif \
  gettext \
  mysqli \
  opcache \
  pcntl \
  sysvmsg \
  sysvsem \
  sysvshm

# RUN cd /tmp \
#   && curl -O https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz \
#   && tar zxvf ioncube_loaders_lin_x86-64.tar.gz \
#   && export PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;") \
#   && export PHP_EXT_DIR=$(php-config --extension-dir) \
#   && cp "./ioncube/ioncube_loader_lin_${PHP_VERSION}.so" "${PHP_EXT_DIR}/ioncube.so" \
#   && rm -rf ./ioncube \
#   && rm ioncube_loaders_lin_x86-64.tar.gz \
#   && docker-php-ext-enable ioncube

RUN pecl channel-update pecl.php.net \
  && pecl install xdebug-3.1.6

RUN docker-php-ext-enable xdebug \
  && sed -i -e 's/^zend_extension/\;zend_extension/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

## Replace next lines with below commented out version once issue is resolved
# https://github.com/php/pecl-networking-ssh2/pull/36
# https://bugs.php.net/bug.php?id=78560
RUN curl -o /tmp/ssh2-1.2.tgz https://pecl.php.net/get/ssh2 \
  && pear install /tmp/ssh2-1.2.tgz \
  && rm /tmp/ssh2-1.2.tgz \
  && docker-php-ext-enable ssh2
#RUN pecl install ssh2-1.2 \
#  && docker-php-ext-enable ssh2

RUN groupadd -g 1000 app \
 && useradd -g 1000 -u 1000 -d /var/www -s /bin/bash app

#Install mhsendmail for mailhog
RUN apt-get update \
  && apt-get install --no-install-recommends -y ca-certificates curl git nano \
  && rm -rf /var/lib/apt/lists/* \
  && curl -Lsf 'https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz' | tar -C '/usr/local' -xvzf -
ENV PATH /usr/local/go/bin:$PATH
RUN go get github.com/mailhog/mhsendmail \
  && cp /root/go/bin/mhsendmail /usr/local/bin/mhsendmail
# RUN curl -sSLO https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 \
#   && chmod +x mhsendmail_linux_amd64 \
#   && mv mhsendmail_linux_amd64 /usr/local/bin/mhsendmail

#Install composer
RUN curl -sS https://getcomposer.org/installer | \
  php -- --install-dir=/usr/local/bin --filename=composer

#Install Blackfire (Optional)
RUN curl -s https://packages.blackfire.io/gpg.key | apt-key add - \
  && echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list \
  && apt-get update \
  && apt-get install blackfire-agent blackfire-php

RUN printf '* *\t* * *\tapp\t%s/usr/local/bin/php /var/www/html/update/cron.php\n' >> /etc/crontab \
  && printf '* *\t* * *\tapp\t%s/usr/local/bin/php /var/www/html/bin/magento cron:run\n' >> /etc/crontab \
  && printf '* *\t* * *\tapp\t%s/usr/local/bin/php /var/www/html/bin/magento setup:cron:run\n#\n' >> /etc/crontab

COPY conf/www.conf /usr/local/etc/php-fpm.d/
COPY conf/php.ini /usr/local/etc/php/
COPY conf/php-fpm.conf /usr/local/etc/
COPY bin/cronstart /usr/local/bin/

RUN mkdir -p /etc/nginx/html /var/www/html /sock \
  && chown -R app:app /etc/nginx /var/www /usr/local/etc/php/conf.d /sock

USER app:app

VOLUME /var/www

WORKDIR /var/www/html

EXPOSE 9001
