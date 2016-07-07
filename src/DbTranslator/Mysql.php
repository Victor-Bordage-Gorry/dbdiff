<?php

namespace DbDiff\DbTranslator;

class Mysql extends \DbDiff\DbTranslator
{
    protected $dictionary = [
        'Field' => self::COLUMN_FIELD,
        'Type' => self::COLUMN_TYPE,
        'Collation' => self::COLUMN_COLLATION,
        'Null' => self::COLUMN_NULL,
        'Key' => self::COLUMN_KEY,
        'Default' => self::COLUMN_DEFAULT,
        'Extra' => self::COLUMN_EXTRA,
        'Privileges' => self::COLUMN_PRIVILEGES,
        'Comment' => self::COLUMN_COMMENT,
        'Table' => self::INDEX_TABLE,
        'Non_unique' => self::INDEX_NON_UNIQUE,
        'Key_name' => self::INDEX_KEY_NAME,
        'Seq_in_index' => self::INDEX_SEQ_IN_INDEX,
        'Column_name' => self::INDEX_COLUMN_NAME,
        'Collation' => self::INDEX_COLLATION,
        'Cardinality' => self::INDEX_CARDINALITY,
        'Sub_part' => self::INDEX_SUB_PART,
        'Packed' => self::INDEX_PACKED,
        'Null' => self::INDEX_NULL,
        'Index_type' => self::INDEX_INDEX_TYPE,
        'Comment' => self::INDEX_COMMENT,
        'Index_comment' => self::INDEX_INDEX_COMMENT,
    ];

    const DB_TYPE = 'Mysql';
}
