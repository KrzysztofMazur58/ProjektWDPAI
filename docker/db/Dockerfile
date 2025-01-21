FROM postgres:15

# Ustawienie zmiennych środowiskowych PostgreSQL
ENV POSTGRES_PASSWORD docker
ENV POSTGRES_DB db
ENV POSTGRES_USER docker

# Instalacja pg_cron
RUN apt-get update && apt-get install -y \
    postgresql-15-cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Konfiguracja pg_cron
RUN echo "cron.database_name = 'db'" >> /usr/share/postgresql/postgresql.conf.sample