<?php
/**
 * File containing the ezcDbSchemaTypesValidator class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

/**
 * ezcDbSchemaTypesValidator validates field definition types.
 *
 * @todo implement from an interface
 * @package DatabaseSchema
 * @version //autogentag//
 */
class ezcDbSchemaTypesValidator
{
    /**
     * Validates if all the types used in the $schema are supported.
     *
     * This method loops over all the fields in a table and checks whether the
     * type that is used for each field is supported. It will return an array
     * containing error strings for each non-supported type that it finds.
     *
     * @param ezcDbSchema $schema
     * @return array(string)
     */
    static public function validate( ezcDbSchema $schema )
    {
        $errors = array();

        /* For each table we check all field's types. */
        foreach ( $schema->getSchema() as $tableName => $table )
        {
            foreach ( $table->fields as $fieldName => $field )
            {
                if ( !in_array( $field->type, ezcDbSchema::$supportedTypes ) )
                {
                    $errors[] = "Field '$tableName:$fieldName' uses the unsupported type '{$field->type}'.";
                }
            }
        }

        return $errors;
    }
}
?>
