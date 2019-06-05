# admin-builder

### Информация

Конструктор таблиц заточенный для CMS IRsite.

### Установка

```
$ composer require avl/admin-builder
```
Или в секцию **require** добавить строчку **"avl/admin-builder": "^1.0"**

```json
{
    "require": {
        "avl/admin-builder": "^1.0"
    }
}
```
### Настройка

Для публикации файла настроек необходимо выполнить команду:

```
$ php artisan vendor:publish --provider="Avl\AdminBuilder\AdminBuilderServiceProvider" --force
```
