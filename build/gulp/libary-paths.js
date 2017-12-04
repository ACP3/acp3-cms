/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

const nodeBasePath = './node_modules',
    systemBasePath = './ACP3/Modules/ACP3/System/Resources/Assets',
    ckeditorBasePath = './ACP3/Modules/ACP3/Wysiwygckeditor/Resources/Assets',
    tinymceBasePath = './ACP3/Modules/ACP3/Wysiwygtinymce/Resources/Assets';

module.exports = [
    {
        'src': [
            nodeBasePath + '/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
            nodeBasePath + '/jquery/dist/jquery.min.js',
            nodeBasePath + '/bootbox/bootbox.js',
            nodeBasePath + '/moment/min/moment.min.js',
            nodeBasePath + '/datatables.net/js/jquery.dataTables.js',
            nodeBasePath + '/datatables.net-bs/js/dataTables.bootstrap.js',
            nodeBasePath + '/bootstrap/dist/js/bootstrap.min.js',
            nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            nodeBasePath + '/html5shiv/dist/html5shiv.min.js',
            nodeBasePath + '/js-cookie/src/js.cookie.js'
        ],
        'dest': systemBasePath + '/js'
    },
    {
        'src': [
            nodeBasePath + '/ckeditor/**/*',
        ],
        'dest': ckeditorBasePath + '/js/ckeditor'
    },
    {
        'src': [
            nodeBasePath + '/tinymce/**/*',
        ],
        'dest': tinymceBasePath + '/js/tinymce'
    },
    {
        'src': [
            nodeBasePath + '/bootstrap/dist/fonts/**/*',
            nodeBasePath + '/font-awesome/fonts/**/*'
        ],
        'dest': systemBasePath + '/fonts'
    },
    {
        'src': [
            nodeBasePath + '/@fancyapps/fancybox/dist/jquery.fancybox.css',
            nodeBasePath + '/bootstrap/dist/css/bootstrap.min.css',
            nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
            nodeBasePath + '/font-awesome/css/font-awesome.css',
            nodeBasePath + '/datatables.net-bs/css/dataTables.bootstrap.css'

        ],
        'dest': systemBasePath + '/css'
    }
];
