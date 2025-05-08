# 🥘 Receptaria

**Receptaria** je webová aplikace vytvořená v PHP frameworku Nette, která umožňuje sdílení receptů a komunikaci mezi uživateli prostřednictvím jednoduchého fóra.

Uživatelé mohou:
- Procházet recepty
- Registrovat se a přihlašovat
- Přidávat, upravovat a mazat vlastní recepty
- Komentovat a diskutovat ve fóru

## 🚀 Použité technologie

- [Nette Framework](https://nette.org/)
- [Tailwind CSS](https://tailwindcss.com/)
- [MySQL](https://www.mysql.com/)
- Latte, Tracy, Composer

## 🛠️ Instalace

1. Naklonuj repozitář:
   ```bash
   git clone https://github.com/uzivatel/receptaria.git
   cd receptaria
   composer install
   cp app/config/config.local.neon.dist app/config/config.local.neon
   mysql -u uživatel -p receptaria < database/receptaria_dump.sql
   php -S localhost:8000 -t www
   http://localhost:8000
   




