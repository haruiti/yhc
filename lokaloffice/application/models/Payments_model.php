<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Payments Model
 *
 * @package Models
 */
class Payments_Model extends CI_Model {
    /**
     * Add a Payment record to the database.
     *
     * This method adds a Payment to the database. If the Payment doesn't exists it is going to be inserted, otherwise
     * the record is going to be updated.
     *
     * @param array $Payment Associative array with the Payment's data. Each key has the same name with the database
     * fields.
     *
     * @return int Returns the Payment id.
     */
    public function add($payment)
    {
        // Validate the payment data before doing anything.
        // $this->validate($payment);

        // :: CHECK IF Payment ALREADY EXIST (ID PagSeguro).
        if ($this->exists($payment))
        {
            // Find the payment id from the database.
            $payment['id'] = $this->find_record_id($payment);
        }

        // :: INSERT OR UPDATE payment RECORD
        if ( ! isset($payment['id']))
        {
            $payment['id'] = $this->_insert($payment);
        }
        else
        {
            $this->_update($payment);
        }
        return $payment['id'];
        
    }

    /**
     * Check if a particular customer record already exists.
     *
     * This method checks whether the given customer already exists in the database. It doesn't search with the id, but
     * with the following fields: "email"
     *
     * @param array $payment Associative array with the customer's data. Each key has the same name with the database
     * fields.
     *
     * @return bool Returns whether the record exists or not.
     *
     * @throws Exception If customer email property is missing.
     */
    public function exists($payment)
    {
        if ( ! isset($payment['id_pagseguro']))
        {
            throw new Exception('Payment\'s id_pagseguro is not provided.');
        }

        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
            ->select('*')
            ->from('ea_payments')
            ->where('ea_payments.id_pagseguro', $payment['id_pagseguro'])
            ->get()->num_rows();

        return ($num_rows > 0) ? TRUE : FALSE;
    }

    /**
     * Insert a new customer record to the database.
     *
     * @param array $customer Associative array with the customer's data. Each key has the same name with the database
     * fields.
     *
     * @return int Returns the id of the new record.
     *
     * @throws Exception If customer record could not be inserted.
     */
    protected function _insert($payment)
    {
        // Before inserting the payment we need to get the payment's role id
        // from the database and assign it to the new record as a foreign key.
        
        $payment['date'] = strtotime('now');
        

        if ( ! $this->db->insert('ea_payments', $payment))
        {
            throw new Exception('Could not insert payment to the database.');
        }

        return (int)$this->db->insert_id();
    }

    /**
     * Update an existing payment record in the database.
     *
     * The payment data argument should already include the record ID in order to process the update operation.
     *
     * @param array $payment Associative array with the payment's data. Each key has the same name with the database
     * fields.
     *
     * @return int Returns the updated record ID.
     *
     * @throws Exception If payment record could not be updated.
     */
    protected function _update($payment)
    {
        // Do not update empty string values.
        
        foreach ($payment as $key => $value)
        {
            if ($value === '')
            {
                unset($payment[$key]);
            }
        }
        
        $this->db->where('id', $payment['id']);
        if ( ! $this->db->update('ea_payments', $payment))
        
        {
            throw new Exception('Could not update payment to the database.');
        }

        return (int)$payment['id'];
    }

    /**
     * Find the database id of a payment record.
     *
     * The payment data should include the following fields in order to get the unique id from the database: "email"
     *
     * IMPORTANT: The record must already exists in the database, otherwise an exception is raised.
     *
     * @param array $payment Array with the payment data. The keys of the array should have the same names as the
     * database fields.
     *
     * @return int Returns the ID.
     *
     * @throws Exception If payment record does not exist.
     */
    public function find_record_id($payment)
    {
        if ( ! isset($payment['id_pagseguro']))
        {
            throw new Exception('Payment\'s id was not provided: '
                . print_r($payment, TRUE));
        }

        // Get payment's role id
        $result = $this->db
            ->select('ea_payments.id')
            ->from('ea_payments')
            ->where('ea_payments.id_pagseguro', $payment['id_pagseguro'])
            ->get();

        if ($result->num_rows() == 0)
        {
            throw new Exception('Could not find payment record id.');
        }

        return $result->row()->id;
    }

    /**
     * Validate customer data before the insert or update operation is executed.
     *
     * @param array $customer Contains the customer data.
     *
     * @return bool Returns the validation result.
     *
     * @throws Exception If customer validation fails.
     */
    public function validate($payment)
    {
        $this->load->helper('data_validation');

        // If a customer id is provided, check whether the record
        // exist in the database.
        if (isset($payment['id_pagseguro']))
        {
            $num_rows = $this->db->get_where('ea_payments',
                ['id_pagseguro' => $payment['id_pagseguro']])->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Provided payment id does not '
                    . 'exist in the database.');
            }
        }
        // Validate required fields
        // if ( ! isset($payment['last_name'])
        //     || ! isset($payment['email'])
        //     || ! isset($payment['phone_number']))
        // {
        //     throw new Exception('Not all required fields are provided: '
        //         . print_r($payment, TRUE));
        // }

        // Validate email address
        // if ( ! filter_var($payment['email'], FILTER_VALIDATE_EMAIL))
        // {
        //     throw new Exception('Invalid email address provided: '
        //         . $payment['email']);
        // }

        // When inserting a record the email address must be unique.
        // $payment_id = (isset($payment['id'])) ? $payment['id'] : '';

        // $num_rows = $this->db
        //     ->select('*')
        //     ->from('ea_users')
        //     ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
        //     ->where('ea_roles.slug', DB_SLUG_CUSTOMER)
        //     ->where('ea_users.email', $payment['email'])
        //     ->where('ea_users.id <>', $payment_id)
        //     ->get()
        //     ->num_rows();

        // if ($num_rows > 0)
        // {
        //     throw new Exception('Given email address belongs to another customer record. '
        //         . 'Please use a different email.');
        // }

        return TRUE;
    }

    /**
     * Delete an existing customer record from the database.
     *
     * @param int $payment The record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception If $payment argument is invalid.
     */
    public function delete($payment_id)
    {
        if ( ! is_numeric($payment_id))
        {
            throw new Exception('Invalid argument type $payment_id: ' . $payment_id);
        }

        $num_rows = $this->db->get_where('ea_payments', ['id' => $payment_id])->num_rows();
        if ($num_rows == 0)
        {
            return FALSE;
        }

        return $this->db->delete('ea_users', ['id' => $payment_id]);
    }

    /**
     * Get a specific row from the appointments table.
     *
     * @param int $payment_id The record's id to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key has the same name as the
     * database field names.
     *
     * @throws Exception If $payment_id argumnet is invalid.
     */
    public function get_row($payment)
    {
        if ( ! is_numeric($payment))
        {
            throw new Exception('Invalid argument provided as $payment_id : ' . $payment);
        }
        return $this->db->get_where('ea_payments', ['id_pagseguro' => $payment])->row_array();
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be returned.
     * @param int $payment The selected record's id.
     *
     * @return string Returns the records value from the database.
     *
     * @throws Exception If $payment argument is invalid.
     * @throws Exception If $field_name argument is invalid.
     * @throws Exception If requested customer record does not exist in the database.
     * @throws Exception If requested field name does not exist in the database.
     */
    public function get_value($field_name, $payment)
    {
        if ( ! is_numeric($payment))
        {
            throw new Exception('Invalid argument provided as $payment: '
                . $payment);
        }

        if ( ! is_string($field_name))
        {
            throw new Exception('$field_name argument is not a string: '
                . $field_name);
        }

        if ($this->db->get_where('ea_payments', ['id' => $payment])->num_rows() == 0)
        {
            throw new Exception('The record with the $payment argument '
                . 'does not exist in the database: ' . $payment);
        }

        $row_data = $this->db->get_where('ea_payments', ['id' => $payment]
        )->row_array();
        if ( ! isset($row_data[$field_name]))
        {
            throw new Exception('The given $field_name argument does not'
                . 'exist in the database: ' . $field_name);
        }

        $customer = $this->db->get_where('ea_payments', ['id' => $payment])->row_array();

        return $customer[$field_name];
    }

    /**
     * Get all, or specific records from appointment's table.
     *
     * @example $this->Model->getBatch('id = ' . $recordId);
     *
     * @param string $whereClause (OPTIONAL) The WHERE clause of the query to be executed. DO NOT INCLUDE 'WHERE'
     * KEYWORD.
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = '')
    {
        $payment = $this->payments();

        if ($where_clause != '')
        {
            $this->db->where($where_clause);
        }

        $this->db->where('id_pagseguro', $payment);

        return $this->db->get('ea_payments')->result_array();
    }


}
