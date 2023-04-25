<?php

namespace App\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
class BaseRequest extends FormRequest
{
    const MAX_CSV_FILE_SIZE = 5120; // 5120 KB = 5 MB
    const MAX_IMAGE_FILE_SIZE = 15360; // 15360 KB = 15MB
    const MAX_SCHEDULE_ATTACH_FILE_SIZE = 20480; // 20480 KB = 20MB
    const IMAGE_MIMES = 'jpg,jpeg,png,bmp';
    const FILE_POST_OFFICE_MIMES = 'jpg,pdf';
    const FILE_POST_OFFICE_SCHEDULE_MIME_TYPES = 'application/pdf';
    const CSV_MIMES = 'csv,txt,xlsx,xls';
    const MAX_TEXT_LENGTH = 2500;
    const MAX_FULL_TEXT_LENGTH = 25000;
    const PHONE_REGEX = "/^[\d]{1,5}-[\d]{1,4}-[\d]{3,4}$/";
    const SLUG_REGEX = "/^[a-z0-9_-]*$/";
    const CODE_REGEX = "/^[0-9-]*$/";
    const APP_VERSION_REGEX = "/^[0-9]((\.)[0-9]){0,2}$/";
    const URL_REGEX = '/^((https|http)?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-]*)*\/?/';
    const PASSWORD_REGEX = '/^(?=.*[0-9])(?=.*[a-zA-Z]).+$/'; // not_used
    const PRICE_REGEX = "/^-?[\d]{1,15}$/";
    const TOTAL_PRICE_REGEX = "/^-?[\d]{1,30}$/";
    const UUID_REGEX = "/^[a-zA-Z0-9]{1,255}$/";
    const UUID_REGEX_1 = "/^[a-zA-Z0-9_-]{1,255}$/";
    const POSTAL_CODE_REGEX = "/^\d{3}-\d{4}$/";
    const POSTAL_CODE_IMPORT_REGEX = "/^\d{3}[-]\d{4}$|^\d{7}$/";
    const KATAKANA_REGEX = "/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|\xEF\xBD[\x9F-\xBF]|\xEF\xBE[\x80-\x9F]|ー| | |　|（|）)+$/";
    const KATAKANA_FULL_REGEX = "/^[ァ-ヶｦ-ﾟー￥¥.\-\/／＼\\\［］\[\]（）()「」ａ-ｚＡ-Ｚ０-９a-zA-Z0-9 　]*$/u";
    const MAX_STEP_ROUTE_APPROVE = 5;
    const MAX_REVIEWER_ROUTE_APPROVE = 5;
    const TEXT_REGEX_NAME = '/^[一-龯ぁ-んァ-ンｧ-ﾝﾞﾟぁ-ゞァ-ヶｦ-ﾟａ-ｚＡ-Ｚ０-９a-zA-Z0-9ー -~]*$/';
    const TEXT_REGEX_DESCRIPTION = '/^[一-龯ぁ-んァ-ンｧ-ﾝﾞﾟぁ-ゞァ-ヶｦ-ﾟａ-ｚＡ-Ｚ０-９a-zA-Z0-9ー -~ \n]*$/';
    const MAX_TAG_LENGTH = 255;
    const MIN_PASSWORD_LENGTH = 8;
    const MAX_PASSWORD_LENGTH = 72;
    const IPV4_REGEX = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
    const HAS_NUMBER_REGEX = '/^(?=.*[0-9])/';
    const HAS_CAPITAL_LETTER_REGEX = '/^(?=.*[A-Z])/';
    const HAS_SYMBOL_REGEX = "/^(?=.*[\!,'`~|_.:<>;-@#=$%()\^&\-\*\{\+\*\=\}\/\\\[\]\"\"\?])/";
    const SYMBOL_ALLOW_REGEX = '/^[一-龯ぁ-んァ-ンｧ-ﾝﾞﾟぁ-ゞァ-ヶｦ-ﾟａ-ｚＡ-Ｚ０-９a-zA-Z0-9ー!_ ,."#$%&()=\-~^\|@`\[\]{}*:;+<>?「」『』｛｝ⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹ！＃＄％＆（）＝ー〜＾￥＠？ 、。〒㈱㈲㈹々ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ]*$/u';
    const SYMBOL_ALLOW_REGEX_HAS_ENTER = '/^[一-龯ぁ-んァ-ンｧ-ﾝﾞﾟぁ-ゞァ-ヶｦ-ﾟａ-ｚＡ-Ｚ０-９a-zA-Z0-9ー!\n_ ,."#$%&()=\-~^\|@`\[\]{}*:;+<>?「」『』｛｝ⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹ！＃＄％＆（）＝ー〜＾￥＠？ 、。〒㈱㈲㈹々ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ]*$/u';
    const DATE_REGEX = "/^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])$/";
    const NUMBER_HALF_WIDTH_REGEX = '/^[0-9]+$/';
    const HALF_WIDTH_REGEX = '/^[A-Za-z0-9]+$/';
    const REGEX_EMAIL = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+$/";
    const REGEX_INVOICE_CODE = "/^[a-zA-Z0-9!@#$%^&*()_+\-=~`\[\]{};'\":|,.<>\/\\\?]*$/";
    const CUSTOM_PRICE_REGEX = '/^[-0-9０-９]+$/';
    const IMPORT_PRICE_REGEX = "/^[-0-9０-９]{1,16}$/u";
    const IMPORT_NUMBER_REGEX = "/^[0-9０-９]{1,9999}$/u";
    const IMPORT_POSTAL_CODE_REGEX = "/^([0-9０-９]{3}-[0-9０-９]{4}|[0-9０-９]{7})$/u";
    const IMPORT_KATAKANA_FULL_REGEX = "/^[ァ-ヶｦ-ﾟー￥¥.\-\/／＼\\\［］\[\]（）()「」ａ-ｚＡ-Ｚ０-９ 　]*$/u";
    const IMPORT_MAJOR_PERSON_NAME_REGEX = "/^[ｧ-ﾝﾞﾟァ-ン|ー|ｰ|ｶ|ｹ|ｳﾞ|ヴ|ヵ|ヶ|ｦ]+$/u";
    const CODE_FORMAT_REGEX = '/^([a-zA-Z0-9!@#%^&*()_+\-=~:"`\'\[\];|,.<>\/\\\?]|(\$|\{|\}|\$\{年}|\$\{月}|\$\{日}|\$\{取引先ID}))+$/';
    const EMAIL_REGEX = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";

    //POST OFFICE
    const MAX_WIDTH_LOGO = 2.5196850394; // inch
    const MAX_HEIGHT_LOGO = 0.94488188976; // inch
    const MAX_DPI = 1200; // pixel

    public static function birthdayBefore()
    {
        return now()->format('Y-m-d');
    }

    /**
     * Common list rules
     *
     * @return array
     */
    public function commonDetailRules()
    {
        return [
            'with' => [
                'nullable',
                'string',
            ],
            'with_count' => [
                'nullable',
                'string',
            ]
        ];
    }

    /**
     * Common list rules
     *
     * @return array
     */
    public function commonListRules()
    {
        return array_merge(self::commonDetailRules(), [
            'page' => [
                'nullable',
                'integer',
            ],
            'limit' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'order' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public static function categoriesErrorMessages()
    {
        return [
            'categories.max' => __('messages.validation.category.max'),

            'categories.*.name.required' => __('messages.validation.category.name.required'),

            'categories.*.uuid.required' => __('messages.validation.category.uuid.required'),
            'categories.*.uuid.distinct' => __('messages.validation.category.uuid.distinct'),
            'categories.*.uuid.regex' => __('messages.validation.category.uuid.regex')
        ];
    }

    public static function filesErrorMessages()
    {
        return [
            'files.max' => __('messages.validation.file.max_file_attached'),
        ];
    }

    public static function productErrorMessages()
    {
        return [
            'uuid.required' => __('messages.validation.product.uuid.required'),
            'uuid.string' => __('messages.validation.product.uuid.string'),
            'uuid.unique' => __('messages.validation.product.uuid.unique'),
            'uuid.regex' => __('messages.validation.product.uuid.regex'),
            'uuid.max' => __('messages.validation.product.uuid.max'),

            'name.required' => __('messages.validation.product.name.required'),
            'name.max' => __('messages.validation.product.name.max'),

            'price.integer' => __('messages.validation.product.price.integer'),
            'price.regex' => __('messages.validation.product.price.regex'),

            'amount.integer' => __('messages.validation.product.amount.integer'),
            'amount.digits_between' => __('messages.validation.product.amount.digits_between'),

            'unit.string' => __('messages.validation.product.unit.string'),
            'unit.max' => __('messages.validation.product.unit.max'),

            'tax_id.integer' => __('messages.validation.product.tax_id.integer'),
            'tax_id.in' => __('messages.validation.product.tax_id.in'),

            'category_id.integer' => __('messages.validation.product.category_id.integer'),
            'category_id.exists' => __('messages.validation.product.category_id.exists'),

            'description.string' => __('messages.validation.product.description.string'),
            'description.max' => __('messages.validation.product.description.max'),

            'sort.integer' => __('messages.validation.product.sort.integer'),
            'sort.digits_between' => __('messages.validation.product.sort.digits_between')
        ];
    }

    public static function templateErrorMessages()
    {
        return [
            'uuid.required' => __('messages.validation.template.uuid.required'),
            'uuid.string' => __('messages.validation.template.uuid.string'),
            'uuid.unique' => __('messages.validation.template.uuid.unique'),
            'uuid.regex' => __('messages.validation.template.uuid.regex'),
            'uuid.max' => __('messages.validation.template.uuid.max'),

            'name.regex' => __('messages.validation.template.name.regex'),

            'description.max' => __('messages.validation.template.description.regex'),
            'description.regex' => __('messages.validation.template.description.max'),

            'sort.digits_between' => __('messages.validation.template.sort.digits_between'),

            'tags.*.regex' => __('messages.validation.template.tag.regex'),

            'items.*.name.max' => __('messages.validation.template.item.name.max'),
            'items.*.unit.max' => __('messages.validation.template.item.unit.max'),

            'setting.company_name.regex' => __('messages.validation.template.setting.company_name.regex'),
            'setting.address.regex' => __('messages.validation.template.setting.address.regex'),
            'setting.person_in_charge.regex' => __('messages.validation.template.setting.person_in_charge.regex'),
            'setting.project_name.regex' => __('messages.validation.template.setting.project_name.regex'),
            'setting.to_address.regex' => __('messages.validation.template.setting.to_address.regex'),
            'setting.to_address.max' => __('messages.validation.template.setting.to_address.max'),
            'setting.note.regex' => __('messages.validation.template.setting.note.regex'),
            'setting.note.max' => __('messages.validation.template.setting.note.max'),

            'setting.logo_id.exists' => __('messages.validation.template.setting.logo_id.exists'),
            'setting.seal_id.exists' => __('messages.validation.template.setting.seal_id.exists'),
            'setting.email.max' => __('messages.validation.template.setting.email.max')
        ];
    }

    public static function employeeErrorMessages()
    {
        return [
            'code.required' => __('messages.validation.employee.code.required'),
            'code.regex' => __('messages.validation.employee.code.regex'),
            'code.max' => __('messages.validation.employee.code.max'),
            'code.unique' => __('messages.validation.employee.code.unique'),

            'first_name.required' => __('messages.validation.employee.first_name.required'),
            'first_name.max' => __('messages.validation.employee.first_name.max'),

            'last_name.required' => __('messages.validation.employee.last_name.required'),
            'last_name.max' => __('messages.validation.employee.last_name.max'),

            'first_name_kana.regex' => __('messages.validation.employee.first_name_kana.regex'),
            'first_name_kana.max' => __('messages.validation.employee.first_name_kana.max'),

            'last_name_kana.regex' => __('messages.validation.employee.last_name_kana.regex'),
            'last_name_kana.max' => __('messages.validation.employee.last_name_kana.max'),

            'email.regex' => __('messages.validation.employee.email.regex'),
            'email.max' => __('messages.validation.employee.email.max'),
            'email.unique' => __('messages.validation.employee.email.unique'),

            'password.confirmed' => __('messages.validation.employee.password.confirmed'),
            'password.required_with' => __('messages.validation.employee.password.confirmed'),

            'joined_on.required' => __('messages.validation.employee.joined_on.required'),
            'joined_on.date_format' => __('messages.validation.employee.joined_on.date_format'),

            'retirement_date.date_format' => __('messages.validation.employee.retirement_date.date_format'),

            'affiliations.*.issued_on.required' => __('messages.validation.employee.affiliation.issued_on.required'),
            'affiliations.*.issued_on.date_format' => __('messages.validation.employee.affiliation.issued_on.date_format'),
            'affiliations.*.issued_on.distinct' => __('messages.validation.employee.affiliation.issued_on.distinct'),
        ];
    }

    public static function companyErrorMessages()
    {
        return [
            'code.required' => __('messages.validation.company.code.required'),
            'name.required' => __('messages.validation.company.name.required'),
            'name.max'      => __('messages.validation.company.name.max.string'),
        ];
    }

    public static function supplierErrorMessages()
    {
        return [
            'code.required' => __('messages.validation.supplier.code.required'),
            'code.regex' => __('messages.validation.supplier.code.regex'),
            'code.max' => __('messages.validation.supplier.code.max'),
            'code.unique' => __('messages.validation.supplier.code.unique'),

            'name.required' => __('messages.validation.supplier.name.required'),
            'name.max' => __('messages.validation.supplier.name.max'),
            'name.regex' => __('messages.validation.supplier.name.regex'),

            'name_kana.regex' => __('messages.validation.supplier.name_kana.regex'),
            'name_kana.max' => __('messages.validation.supplier.name_kana.max'),

            'status.max' => __('messages.validation.supplier.status.max'),

            'major_person_name.max' => __('messages.validation.supplier.major_person_name.max'),
            'major_person_name.regex' => __('messages.validation.supplier.major_person_name.regex'),

            'major_person_name_kana.regex' => __('messages.validation.supplier.major_person_name_kana.regex'),
            'major_person_name_kana.max' => __('messages.validation.supplier.major_person_name_kana.max'),

            'major_jobtitle_name.max' => __('messages.validation.supplier.major_jobtitle_name.max'),
            'major_jobtitle_name.regex' => __('messages.validation.supplier.major_jobtitle_name.regex'),

            'major_department_name.max' => __('messages.validation.supplier.major_department_name.max'),
            'major_department_name.regex' => __('messages.validation.supplier.major_department_name.regex'),

            'email.email' => __('messages.validation.supplier.email.email'),
            'email.max' => __('messages.validation.supplier.email.max'),
            'email.regex' => __('messages.validation.supplier.email.email'),

            'postal_code.regex' => __('messages.validation.supplier.postal_code.regex'),
            'postal_code.max' => __('messages.validation.supplier.postal_code.max'),

            'address.max' => __('messages.validation.supplier.address.max'),
            'address.regex' => __('messages.validation.supplier.address.regex'),

            'supplier_category_id.exists' => __('messages.validation.supplier.supplier_category_id.exists'),

            'object_send.in' => __('messages.validation.supplier.object_send.in'),

            'cc_emails.max'              => __('messages.validation.supplier.cc_emails.max'),
            'cc_emails.array'            => __('messages.validation.supplier.cc_emails.array'),
            'cc_emails.*.email.required' => __('messages.validation.supplier.cc_emails.email.required'),
            'cc_emails.*.email.distinct' => __('messages.validation.supplier.cc_emails.email.exists'),
            'cc_emails.*.email.regex'    => __('messages.validation.supplier.cc_emails.email.regex'),
            'cc_emails.*.email.max'      => __('messages.validation.supplier.cc_emails.email.max'),
        ];
    }

    public static function supplierImportErrorMessages()
    {
        return [
            'code.required' => 'import.supplier.code.required',
            'code.regex'    => 'import.supplier.code.regex',
            'code.max'      => 'import.supplier.code.max',
            'code.unique'   => 'import.supplier.code.unique',
            'code.exists'   => 'import.supplier.code.exists',

            'name.required' => 'import.supplier.name.required',
            'name.max'      => 'import.supplier.name.max',
            'name.regex'    => 'import.supplier.name.regex',

            'name_kana.regex' => 'import.supplier.name_kana.regex',
            'name_kana.max'   => 'import.supplier.name_kana.max',

            'status.in' => 'import.supplier.status.in',
            'status.integer' => 'import.supplier.status.in',
            'status.required' => 'import.supplier.status.required',

            'major_person_name.max'   => 'import.supplier.major_person_name.max',
            'major_person_name.regex' => 'import.supplier.major_person_name.regex',

            'major_person_name_kana.regex' => 'import.supplier.major_person_name_kana.regex',
            'major_person_name_kana.max'   => 'import.supplier.major_person_name_kana.max',

            'major_jobtitle_name.max'   => 'import.supplier.major_jobtitle_name.max',
            'major_jobtitle_name.regex' => 'import.supplier.major_jobtitle_name.regex',

            'major_department_name.max'   => 'import.supplier.major_department_name.max',
            'major_department_name.regex' => 'import.supplier.major_department_name.regex',

            'email.regex' => 'import.supplier.email.email',
            'email.max'   => 'import.supplier.email.max',

            'postal_code.regex' => 'import.supplier.postal_code.regex',

            'address.max'   => 'import.supplier.address.max',
            'address.regex' => 'import.supplier.address.regex',

            'display_order.digits_between' => 'import.supplier.display_order.digits_between',
            'display_order.regex' => 'import.supplier.display_order.regex',
            'display_order.integer' => 'import.supplier.display_order.regex',

            'supplier_category_code.exists' => 'import.supplier.supplier_category_code.exists',
            'supplier_category_code.max' => 'import.supplier.supplier_category_code.max',
            'supplier_category_code.regex' => 'import.supplier.supplier_category_code.regex',

            'city_name.max' => 'import.supplier.city_name.max',
            'prefecture_name.max' => 'import.supplier.prefecture_name.max',

            'object_send.in' => 'import.supplier.object_send.in',
            'object_send.integer' => 'import.supplier.object_send.in',
            'object_send.required' => 'import.supplier.object_send.required',

            'cc_emails.max' => 'import.supplier.cc_emails.max',
            'cc_emails.*.max' => 'import.supplier.cc_emails.email.max',
            'cc_emails.*.required' => 'import.supplier.cc_emails.email.required',
            'cc_emails.*.regex' => 'import.supplier.cc_emails.email.regex',
            'cc_emails.*.distinct' => 'import.supplier.cc_emails.email.distinct',
        ];
    }

    public static function supplierCategoryErrorMessages()
    {
        return [
            'categories.*.id.exists' => __('messages.validation.supplier_category.id.exists'),
        ];
    }

    public static function invoiceErrorMessages()
    {
        return [
            'deadline_date.required' => __('messages.validation.invoice.deadline_date.required'),
            'deadline_date.date_format' => __('messages.validation.invoice.deadline_date.date_format'),
            'template_id.exists' => __('messages.validation.invoice.template_id.exists'),
            'send_mail_date.date_format' => __('messages.validation.invoice.send_mail_date.date_format'),
            'send_office_date.date_format' => __('messages.validation.invoice.send_office_date.date_format'),
            'send_money_date.date_format' => __('messages.validation.invoice.send_money_date.date_format'),
            'disable_date.date_format' => __('messages.validation.invoice.disable_date.date_format'),

            'setting.payment_date.date_format' => __('messages.validation.invoice.setting.payment_date.date_format'),
            'setting.payment_term.date_format' => __('messages.validation.invoice.setting.payment_term.date_format'),
            'description.max' => __('messages.validation.invoice.description.max'),
            'description.regex' => __('messages.validation.invoice.description.regex'),
            'setting.code.max' => __('messages.validation.invoice.setting.code.max'),
            'setting.code.required' => __('messages.validation.invoice.setting.code.required'),
            'setting.supplier_id.required' => __('messages.validation.invoice.setting.supplier_id.required'),
            'setting.supplier_id.exists' => __('messages.validation.invoice.setting.supplier_id.exists'),
            'setting.supplier_info.regex' => __('messages.validation.invoice.setting.supplier_info.regex'),
            'setting.company_name.regex' => __('messages.validation.invoice.setting.company_name.regex'),
            'setting.address.regex' => __('messages.validation.invoice.setting.address.regex'),
            'setting.person_in_charge.regex' => __('messages.validation.invoice.setting.person_in_charge.regex'),
            'setting.project_name.regex' => __('messages.validation.invoice.setting.project_name.regex'),
            'setting.to_address.regex' => __('messages.validation.invoice.setting.to_address.regex'),
            'setting.to_address.max' => __('messages.validation.invoice.setting.to_address.max'),
            'setting.note.regex' => __('messages.validation.invoice.setting.note.regex'),
            'setting.note.max' => __('messages.validation.invoice.setting.note.max'),
            'setting.logo_id.exists' => __('messages.validation.invoice.setting.logo_id.exists'),
            'setting.seal_id.exists' => __('messages.validation.invoice.setting.seal_id.exists'),
            'setting.email.max' => __('messages.validation.invoice.setting.email.max'),

            'items.*.name.max' => __('messages.validation.invoice.item.name.max'),
            'items.*.unit.max' => __('messages.validation.invoice.item.unit.max'),
            'tags.*.regex' => __('messages.validation.invoice.tag.regex'),
            'related_document.regex' => __('messages.validation.invoice.related_document.regex'),
            'accounting_dept_id.exists' => __('messages.validation.invoice.accounting_dept_id.exists'),
        ];
    }

    public static function routeApproveErrorMessages()
    {
        return [
            'code.required' => __('messages.validation.route_approve.code.required'),
            'code.unique' => __('messages.validation.route_approve.code.unique'),
            'code.regex' => __('messages.validation.route_approve.code.regex'),
            'description.max' => __('messages.validation.route_approve.description.max'),
            'description.regex' => __('messages.validation.route_approve.description.regex'),
            'name.regex' => __('messages.validation.route_approve.name.regex'),
            'code.max' => __('messages.validation.route_approve.code.max'),
            'name.max' => __('messages.validation.route_approve.name.max'),
            'steps.*.reviewer_ids.max' => __('messages.validation.route_approve.step.reviewer_ids.max'),
            'steps.max' => __('messages.validation.route_approve.step.max'),
            'steps.required' => __('messages.validation.route_approve.step.required'),
            'steps.*.reviewer_ids.*.exists' => __('messages.validation.route_approve.step.reviewer_ids.exists'),
        ];
    }

    public static function sendMailErrorMessages()
    {
        return [
            'invoices.*.title.max' => __('messages.validation.invoice.send_mail.title.max'),
            'invoices.*.body.max' => __('messages.validation.invoice.send_mail.body.max'),
            'invoices.*.cc_mails.max' => __('messages.validation.invoice.send_mail.cc_mails.max'),
            'invoices.*.cc_mails.*.regex' => __('messages.validation.invoice.send_mail.cc_mails.email.regex'),
            'invoices.*.cc_mails.*.max' => __('messages.validation.invoice.send_mail.cc_mails.email.max'),
            'invoices.*.replies.max' => __('messages.validation.invoice.send_mail.replies.max'),
            'invoices.*.replies.*.regex' => __('messages.validation.invoice.send_mail.replies.email.regex'),
            'invoices.*.replies.*.max' => __('messages.validation.invoice.send_mail.replies.email.max'),
            'invoices.*.receivers.max' => __('messages.validation.invoice.send_mail.receivers.max'),
            'invoices.*.receivers.*.regex' => __('messages.validation.invoice.send_mail.receivers.email.regex'),
            'invoices.*.receivers.*.max' => __('messages.validation.invoice.send_mail.receivers.email.max'),
            'invoices.*.receivers.required' => __('messages.validation.invoice.send_mail.receivers.required'),
            'invoices.*.id.exists' => __('messages.validation.invoice.send_mail.id.exists'),
        ];
    }

    public static function forgotPasswordErrorMessages()
    {
        return [
            'email.regex' => __('messages.validation.password.forgot_password.email.regex'),
            'email.max' => __('messages.validation.password.forgot_password.email.max'),
            'email.exists' => __('messages.validation.password.forgot_password.email.exists'),
        ];
    }

    public static function objectRouteApproveErrorMessages()
    {
        return [
            'note.max' => __('messages.validation.object_route_approve.note.max'),
        ];
    }

    public static function invoiceUpdateStatusErrorMessages()
    {
        return [
            'ids.*.exists' => __('messages.validation.invoice.update_status.ids.exists'),
        ];
    }

    public static function invoiceDeleteErrorMessages()
    {
        return [
            'ids.*.exists' => __('messages.validation.invoice.ids.exists'),
        ];
    }

    public static function passwordPolicyErrorMessages()
    {
        return [
            'min_length.required' => __('messages.validation.password_policy.min_length.required'),
            'min_length.max'      => __('messages.validation.password_policy.min_length.max.numeric'),
            'min_length.min'      => __('messages.validation.password_policy.min_length.min.numeric')
        ];
    }

    public static function accessPermissionErrorMessages()
    {
        return [
            'ips.required_if' => __('messages.validation.access_permission.ips.required_if'),
            'ips.array'       => __('messages.validation.access_permission.ips.array'),
            'ips.*.max'       => __('messages.validation.access_permission.ips.max')
        ];
    }

    public static function accountingDepartmentErrorMessages()
    {
        return [
            'code.required'                => __('messages.validation.accounting_department.code.required'),
            'code.max'                     => __('messages.validation.accounting_department.code.max'),
            'code.unique'                  => __('messages.validation.accounting_department.code.unique'),
            'code.regex'                   => __('messages.validation.accounting_department.code.regex'),
            'name.required'                => __('messages.validation.accounting_department.name.required'),
            'name.max'                     => __('messages.validation.accounting_department.name.max'),
            'short_name.required'          => __('messages.validation.accounting_department.short_name.required'),
            'short_name.max'               => __('messages.validation.accounting_department.short_name.max'),
            'status.array'                 => __('messages.validation.accounting_department.status.array'),
            'display_order.digits_between' => __('messages.validation.accounting_department.display_order.digits_between')
        ];
    }

    public static function departmentErrorMessages()
    {
        return [
            'code.required' => __('messages.validation.department.code.required'),
            'code.unique' => __('messages.validation.department.code.unique'),
            'code.regex' => __('messages.validation.department.code.regex'),
            'code.max' => __('messages.validation.department.code.max'),

            'name.required' => __('messages.validation.department.name.max'),
            'name.max' => __('messages.validation.department.name.max'),

            'abbrev.max' => __('messages.validation.department.abbrev.max'),

            'kana.regex' => __('messages.validation.department.kana.regex'),

            'deprecated_date.date_format' => __('messages.validation.department.deprecated_date.date_format'),
            'deprecated_date.after_or_equal' => __('messages.validation.department.deprecated_date.after_or_equal'),

            'date_of_establishment.date_format' => __('messages.validation.department.date_of_establishment.date_format'),
            'date_of_establishment.required' => __('messages.validation.department.date_of_establishment.date_format'),

            'display_order.digits_between' => __('messages.validation.department.display_order.digits_between'),
        ];
    }

    public static function importErrorMessages()
    {
        return [
            'file.required'        => __('messages.validation.import.file.required'),
            'file.file'            => __('messages.validation.import.file.file'),
            'file.mimes'           => __('messages.validation.import.file.mimes'),
            'file.max'             => __('messages.validation.import.file.max'),
            'action_type.required' => __('messages.validation.import.action_type.required'),
            'format_type.required' => __('messages.validation.import.format_type.required'),
        ];
    }

    public static function exportErrorMessages()
    {
        return [
            'deadline_date_from.required' => __('messages.validation.export.deadline_date_from.required'),
            'deadline_date_to.required'   => __('messages.validation.export.deadline_date_to.required'),
        ];
    }

    public static function authErrorMessages()
    {
        return [
            'code_or_email.max' => __('messages.validation.auth.code_or_email.max')
        ];
    }

    public static function validateProductImportErrorMessages()
    {
        return [
            'uuid.required' => 'import.product.uuid.required',
            'uuid.unique' => 'import.product.uuid.unique',
            'uuid.regex' => 'import.product.uuid.regex',
            'uuid.max' => 'import.product.uuid.max',

            'name.required' => 'import.product.name.required',
            'name.max' => 'import.product.name.max',

            'price.digits_between' => 'import.product.price.digits_between',
            'price.regex' => 'import.product.price.regex',

            'amount.digits_between' => 'import.product.amount.digits_between',
            'amount.regex' => 'import.product.amount.regex',

            'unit.max' => 'import.product.unit.max',

            'tax_id.in' => 'import.product.tax_id.in',
            'tax_id.regex' => 'import.product.tax_id.regex',

            'category_uuid.max' => 'import.product.category_uuid.max',
            'category_uuid.exists' => 'import.product.category_uuid.exists',
            'category_uuid.regex' => 'import.product.category_uuid.regex',

            'description.max' => 'import.product.description.max',

            'sort.digits_between' => 'import.product.sort.digits_between',
            'sort.regex' => 'import.product.sort.regex',
        ];
    }

    public static function invoiceImportErrorMessages()
    {
        $common = self::importErrorMessages();
        $rules = [
            'is_product.required' => 'import.invoice.is_product.required',
            'is_product.in' => 'import.invoice.is_product.in',
            'invoice.required' => 'import.invoice.required',
            'invoice.max' => 'import.invoice.max',
            'invoice.regex' => 'import.invoice.regex',
            'invoice.exists' => 'import.invoice.exists',
            'deadline_date.required' => 'import.invoice.deadline_date.required',
            'deadline_date.required_if' => 'import.invoice.deadline_date.required',
            'deadline_date.regex' => 'import.invoice.deadline_date.date_format',
            'accounting_dept_code.regex' => 'import.invoice.accounting_dept_code.regex',
            'accounting_dept_code.max' => 'import.invoice.accounting_dept_code.max',
            'accounting_dept_code.exists' => 'import.invoice.accounting_dept_code.exists',
            'tags.*.max' => 'import.invoice.tags.max',
            'setting.code.required' => 'import.invoice.setting.code.required',
            'setting.code.max' => 'import.invoice.setting.code.max',
            'setting.code.regex' => 'import.invoice.setting.code.regex',
            'setting.code.exists' => 'import.invoice.setting.code.exists',
            'setting.code.unique' => 'import.invoice.setting.code.unique',
            'setting.supplier_code.required' => 'import.invoice.setting.supplier_code.required',
            'setting.supplier_code.required_if' => 'import.invoice.setting.supplier_code.required',
            'setting.supplier_code.max' => 'import.invoice.setting.supplier_code.max',
            'setting.supplier_code.exists' => 'import.invoice.setting.supplier_code.exists',
            'setting.supplier_code.regex' => 'import.invoice.setting.supplier_code.regex',
            'setting.supplier_opt.required' => 'import.invoice.setting.supplier_opt.required',
            'setting.supplier_opt.required_if' => 'import.invoice.setting.supplier_opt.required',
            'setting.supplier_opt.in' => 'import.invoice.setting.supplier_opt.in',
            'description.max' => 'import.invoice.setting.description.max',
            'setting.payment_date.regex' => 'import.invoice.setting.payment_date.regex',
            'setting.payment_term.regex' => 'import.invoice.setting.payment_term.regex',
            'setting.company_name.max' => 'import.invoice.setting.company_name.max',
            'setting.postal_code.max' => 'import.invoice.setting.postal_code.max',
            'setting.postal_code.regex' => 'import.invoice.setting.postal_code.regex',
            'setting.address.max' => 'import.invoice.setting.address.max',
            'setting.phone.regex' => 'import.invoice.setting.phone.regex',
            'setting.email.max' => 'import.invoice.setting.email.max',
            'setting.email.regex' => 'import.invoice.setting.email.email',
            'setting.person_in_charge.max' => 'import.invoice.setting.person_in_charge.max',
            'setting.supplier_postal_code.regex' => 'import.invoice.setting.supplier_postal_code.regex',
            'setting.supplier_address.max' => 'import.invoice.setting.supplier_address.max',
            'setting.supplier_department.max' => 'import.invoice.setting.supplier_department.max',
            'setting.supplier_major_job_title.max' => 'import.invoice.setting.supplier_major_job_title.max',
            'setting.supplier_major_person.max' => 'import.invoice.setting.supplier_major_person.max',
            'setting.project_name.max' => 'import.invoice.setting.project_name.max',
            'setting.to_address.max' => 'import.invoice.setting.to_address.max',
            'setting.note.max' => 'import.invoice.setting.note.max',
            'items.*.type.required' => 'import.invoice.items.type.required',
            'items.*.type.in' => 'import.invoice.items.type.in',
            'items.*.uuid.max' => 'import.invoice.items.uuid.max',
            'items.*.uuid.regex' => 'import.invoice.items.uuid.regex',
            'items.*.uuid.exists' => 'import.invoice.items.uuid.exists',
            'items.*.name.max' => 'import.invoice.items.name.max',
            'items.*.price.max' => 'import.invoice.items.price.max',
            'items.*.price.digits_between' => 'import.invoice.items.price.digits_between',
            'items.*.price.regex' => 'import.invoice.items.price.regex',
            'items.*.amount.regex' => 'import.invoice.items.amount.regex',
            'items.*.amount.max' => 'import.invoice.items.amount.max',
            'items.*.unit.max' => 'import.invoice.items.unit.max',
            'items.*.tax_id.in' => 'import.invoice.items.tax_id.in',
        ];

        return array_merge($common, $rules);
    }

    public static function saveTemplateMastserSettinMessages()
    {
        return [
            'master_setting.*.code_format.regex' => __('messages.validation.template_master_setting.code_format.regex'),
            'delete_file_ids.*.exists' => __('messages.validation.template_master_setting.delete_file_ids.exists'),
            'active_file_ids.*.exists' => __('messages.validation.template_master_setting.active_file_ids.exists'),
        ];
    }

    public static function getId($key)
    {
        $id = request('id');
        if (!empty($id)) {
            return $id;
        }
        $object = request($key);
        return optional($object)->id;
    }

    /**
     * Define content message errors
     *
     * @return array
     */
    public static function postOfficeEnvelopAddressSettingErrorMessages()
    {
        return [
            'comment_1.max' => __('messages.validation.post_office_envelop_address_setting.comment_1.max'),
            'comment_2.max' => __('messages.validation.post_office_envelop_address_setting.comment_2.max'),
        ];
    }

    /**
     * Define content message errors
     *
     * @return array
     */
    public static function postOfficeCreateErrorMessages()
    {
        return [
            'schedules.*.receiver.address_1.max'     => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.address_2.max'     => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.name_1.max'        => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.name_2.max'        => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.name_3.max'        => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.name_4.max'        => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.comment_1.max'     => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.comment_2.max'     => __('messages.validation.post_office_schedule.receiver_info_limit_60_char'),
            'schedules.*.receiver.postal_code.regex' => __('messages.validation.post_office_schedule.postal_code.regex'),
        ];
    }
}
