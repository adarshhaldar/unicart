<?php

namespace Unicart\Lang;

// Russian translation
return [
    'invalid_validator' => 'Неверный валидатор для валидации',
    'unicart_exceptions' => [
        'empty_cart' => 'Корзина пуста',
        'float_item_id_not_allowed' => 'Десятичные значения не разрешены в качестве идентификаторов товаров. Id: :id.',
        'invalid_item_price' => 'Цена для товара с Id: :id не может быть меньше или равна 0.',
        'invalid_item_qty' => 'Количество для товара с Id: :id не может быть меньше или равно 0.',
        'item_exist' => 'Товар с Id: :id уже существует.',
        'item_doesnt_exist' => 'Товар с Id: :id не существует.',
        'invalid_item_upto_amount' => 'Неверная максимальная сумма скидки для товара с Id: :id.',
        'cannt_apply_on_cart_due_to_item' => 'Невозможно применить :applying к корзине, так как на товар с Id: :id уже применён налог.',
        'cannt_add_in_cart_due_to_item' => 'Невозможно добавить :applying в корзину после её инициализации для товара с Id: :id.',
        'invalid_cart_upto_amount' => 'Неверная максимальная сумма скидки для корзины.',
        'cannt_add_discount_after_tax' => 'Невозможно добавить скидку после налогообложения.',
        'cannt_add_discount_after_delivery' => 'Невозможно добавить скидку после добавления стоимости доставки.',
        'cannt_add_another_delivery' => 'Невозможно добавить ещё одну доставку в корзину.',
        'invalid_buy_get_qty_for_item' => 'Количество для покупки или получения не может быть меньше или равно 0 для товара с Id: :id.',
        'invalid_qty_for_item_bxgy' => 'Количество товара недостаточно для указанных значений BxGy для товара с Id: :id.',
        'cannt_apply_sxgy' => 'Невозможно применить ещё одну скидку SxGy к корзине.',
        'invalid_sxgy' => 'Сумма для SpendXGetY не может быть меньше или равна 0.',
        'invalid_spend_in_sxgy' => 'Сумма расходов не может быть меньше суммы получения для SpendXGetY скидки.',
        'discount_stacking_disabled' => 'Сложение скидок отключено. Скидка уже была применена.',
        'invalid_discount_percentage_for_item' => 'Процент скидки не может быть меньше или равен 0 для товара с Id: :id.',
        'invalid_discount_percentage' => 'Процент скидки не может быть меньше или равен 0.',
        'invalid_discount_for_item' => 'Скидка не может быть меньше или равна 0 для товара с Id: :id.',
        'invalid_discount' => 'Скидка не может быть меньше или равна 0.',
        'invalid_delivery_charge_for_item' => 'Стоимость доставки не может быть меньше или равна 0 для товара с Id: :id.',
        'invalid_delivery_charge' => 'Стоимость доставки не может быть меньше или равна 0.',
        'invalid_tax_for_item' => 'Налог не может быть меньше или равен 0 для товара с Id: :id.',
        'invalid_tax' => 'Налог не может быть меньше или равен 0.',
    ],
    'item_exceptions' => [
        'invalid_id' => 'Десятичные значения не разрешены в качестве идентификаторов товаров. Id: :id.',
        'invalid_price' => 'Цена для товара с Id: :id не может быть меньше или равна 0.',
        'invalid_qty' => 'Количество для товара с Id: :id не может быть меньше или равно 0.',
        'invalid_upto_amount' => 'Неверная максимальная сумма скидки для товара с Id: :id.',
        'cannt_add_discount_after_tax' => 'Невозможно добавить скидку после налогообложения для товара с Id: :id.',
        'cannt_add_discount_after_delivery_charge' => 'Невозможно добавить скидку после добавления стоимости доставки для товара с Id: :id.',
        'cannt_add_delivery_charge' => 'Невозможно добавить ещё одну стоимость доставки для товара с Id: :id.',
        'cannt_add_bxgy_after_bxgy' => 'Невозможно добавить BxGy после уже добавленного BxGy для товара с Id: :id.',
        'cannt_add_bxgy_after_dicount' => 'Невозможно добавить BxGy после добавления другой скидки для товара с Id: :id.',
        'cannt_add_discount_after_bxgy' => 'Невозможно добавить скидку после добавления BxGy для товара с Id: :id.',
        'invalid_bxgy_qty' => 'Количество для BxGy не может быть отрицательным для товара с Id: :id.',
        'dissatified_bxgy_qty' => 'Количество не соответствует требованиям для BxGy для товара с Id: :id.',
        'discount_stacking_disabled' => 'Сложение скидок отключено. У этого товара уже есть скидка.',
        'invalid_discount_percentage' => 'Процент скидки не может быть меньше или равен 0 для товара с Id: :id.',
        'invalid_discount' => 'Скидка не может быть меньше или равна 0 для товара с Id: :id.',
        'invalid_delivery_charge' => 'Стоимость доставки не может быть меньше или равна 0 для товара с Id: :id.',
        'invalid_tax' => 'Налог не может быть меньше или равен 0 для товара с Id: :id.'
    ]
];
