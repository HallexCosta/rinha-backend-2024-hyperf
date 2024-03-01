FROM hyperf/hyperf:8.2-alpine-v3.18-swoole
ENV timezone="America/Sao_Paulo" \
  APP_ENV=prod \
  SCAN_CACHEABLE=true

WORKDIR /opt/www
COPY . .

RUN set -ex \
  # define the docker timezone
  && ln -sf /usr/share/zoneinfo/${timezone} /etc/localtime \
  && echo "${timezone}" > /etc/timezone \
  && apk --no-cache add php82-pdo_pgsql

FROM hyperf/hyperf:8.2-alpine-v3.18-swoole
COPY --from=0 /opt/www .
RUN composer install --prefer-dist --no-dev --optimize-autoloader

EXPOSE 9501
ENTRYPOINT ["php", "/opt/www/bin/hyperf.php" ]
CMD [ "start" ]
