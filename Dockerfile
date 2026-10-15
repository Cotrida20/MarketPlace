FROM php:8.3-apache
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
ENV PORT=10000
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf
EXPOSE 10000
