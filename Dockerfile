FROM debian

RUN apt-get update && apt-get upgrade
RUN apt-get -y install tzdata
RUN apt-get -y install apache2
RUN apt-get -y install curl
RUN curl -L -o kk.tgz https://go.dev/dl/go1.22.3.linux-amd64.tar.gz
RUN chmod 777 kk.tgz
RUN mv kk.tgz /usr/local/
WORKDIR /usr/local
RUN tar -xzf kk.tgz
RUN export PATH=”$PATH:/usr/local/go/bin”


RUN echo "Dockerfile Test on Apache2" > /var/www/html/index.html



EXPOSE 80

CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]


