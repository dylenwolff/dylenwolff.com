<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Award;
use App\Models\ClientDocument;
use App\Models\ClientProject;
use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\CrmTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MaintenancePlan;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\OutreachEmail;
use App\Models\Project;
use App\Models\Qualification;
use App\Models\Quote;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SupportTicket;
use App\Models\WhatsAppOutreach;

final class WorkspaceResources
{
    public static function all(): array
    {
        return [
            'organizations' => ['label' => 'Organizations', 'singular' => 'Organization', 'model' => Organization::class, 'title' => 'name', 'columns' => ['name', 'industry', 'country_code', 'email', 'status']],
            'contacts' => ['label' => 'Contacts', 'singular' => 'Contact', 'model' => Contact::class, 'title' => 'full_name', 'columns' => ['full_name', 'organization.name', 'email', 'phone', 'status']],
            'leads' => ['label' => 'Leads', 'singular' => 'Lead', 'model' => Lead::class, 'title' => 'title', 'columns' => ['title', 'organization.name', 'organization.country_code', 'source', 'status', 'estimated_value', 'next_follow_up_at']],
            'opportunities' => ['label' => 'Opportunities', 'singular' => 'Opportunity', 'model' => Opportunity::class, 'title' => 'name', 'columns' => ['name', 'organization.name', 'organization.country_code', 'stage', 'amount', 'probability', 'expected_close_date']],
            'outreach-emails' => ['label' => 'Email queue', 'singular' => 'Email', 'model' => OutreachEmail::class, 'title' => 'subject', 'columns' => ['organization.name', 'recipient_email', 'subject', 'status', 'follow_up_at', 'sent_at'], 'readonly' => ['sent_at', 'external_message_id']],
            'whatsapp-outreaches' => ['label' => 'WhatsApp queue', 'singular' => 'WhatsApp message', 'model' => WhatsAppOutreach::class, 'title' => 'recipient_name', 'columns' => ['organization.name', 'recipient_name', 'phone', 'status', 'opened_at', 'sent_at'], 'create' => false, 'readonly' => ['opened_at', 'sent_at']],
            'crm-tasks' => ['label' => 'Tasks', 'singular' => 'Task', 'model' => CrmTask::class, 'title' => 'title', 'columns' => ['title', 'organization_name', 'contact_name', 'contact_phone', 'status', 'priority', 'due_at']],
            'activities' => ['label' => 'Activities', 'singular' => 'Activity', 'model' => Activity::class, 'title' => 'subject', 'columns' => ['occurred_at', 'type', 'subject', 'contact.full_name', 'opportunity.name']],
            'client-projects' => ['label' => 'Client projects', 'singular' => 'Client project', 'model' => ClientProject::class, 'title' => 'name', 'columns' => ['name', 'organization.name', 'status', 'billing_type', 'fee', 'deadline']],
            'support-tickets' => ['label' => 'Support tickets', 'singular' => 'Support ticket', 'model' => SupportTicket::class, 'title' => 'subject', 'columns' => ['subject', 'organization.name', 'priority', 'status', 'due_at']],
            'client-documents' => ['label' => 'Documents', 'singular' => 'Document', 'model' => ClientDocument::class, 'title' => 'title', 'columns' => ['title', 'organization.name', 'clientProject.name', 'type', 'updated_at']],
            'quotes' => ['label' => 'Quotes', 'singular' => 'Quote', 'model' => Quote::class, 'title' => 'number', 'columns' => ['number', 'organization.name', 'status', 'total', 'issued_at', 'valid_until']],
            'invoices' => ['label' => 'Invoices', 'singular' => 'Invoice', 'model' => Invoice::class, 'title' => 'number', 'columns' => ['number', 'organization.name', 'status', 'total', 'amount_paid', 'due_at']],
            'maintenance-plans' => ['label' => 'Maintenance plans', 'singular' => 'Maintenance plan', 'model' => MaintenancePlan::class, 'title' => 'name', 'columns' => ['name', 'organization.name', 'status', 'monthly_fee', 'next_billing_date']],
            'projects' => ['label' => 'Portfolio projects', 'singular' => 'Project', 'model' => Project::class, 'title' => 'title', 'columns' => ['title', 'category', 'is_featured', 'sort_order', 'is_published']],
            'services' => ['label' => 'Services', 'singular' => 'Service', 'model' => Service::class, 'title' => 'title', 'columns' => ['title', 'description', 'sort_order', 'is_published']],
            'awards' => ['label' => 'Awards & recognition', 'singular' => 'Award', 'model' => Award::class, 'title' => 'title', 'columns' => ['title', 'placement', 'issuer', 'awarded_at', 'is_featured', 'is_published']],
            'qualifications' => ['label' => 'Qualifications', 'singular' => 'Qualification', 'model' => Qualification::class, 'title' => 'title', 'columns' => ['title', 'institution', 'credential_type', 'end_year', 'is_published']],
            'contact-messages' => ['label' => 'Messages', 'singular' => 'Message', 'model' => ContactMessage::class, 'title' => 'subject', 'columns' => ['name', 'email', 'subject', 'lead.title', 'status', 'created_at'], 'create' => false, 'readonly' => ['name', 'email', 'subject', 'message']],
            'site-settings' => ['label' => 'Site content', 'singular' => 'Site content', 'model' => SiteSetting::class, 'title' => 'name', 'columns' => ['name', 'professional_title', 'updated_at'], 'singleton' => true],
        ];
    }

    public static function get(string $section): array
    {
        abort_unless(isset(self::all()[$section]), 404);

        return self::all()[$section];
    }

    public static function options(string $section, string $field): ?array
    {
        if ($field === 'country_code') {
            return Countries::options();
        }

        $relationships = [
            'organization_id' => [Organization::class, 'name'], 'contact_id' => [Contact::class, 'full_name'],
            'lead_id' => [Lead::class, 'title'], 'opportunity_id' => [Opportunity::class, 'name'],
            'client_project_id' => [ClientProject::class, 'name'],
        ];
        if (isset($relationships[$field])) {
            [$model, $label] = $relationships[$field];

            return $model::query()->get()->mapWithKeys(fn ($record) => [$record->getKey() => data_get($record, $label)])->all();
        }

        $key = $section.'.'.$field;

        return match ($key) {
            'organizations.status' => self::pairs(['prospect', 'client', 'partner', 'inactive']),
            'contacts.status' => self::pairs(['active', 'inactive']),
            'leads.source' => self::pairs(['website', 'referral', 'upwork', 'fiverr', 'outreach', 'manual']),
            'leads.status' => self::pairs(['new', 'contacted', 'qualified', 'unqualified', 'converted']),
            'opportunities.stage' => self::pairs(['prospecting', 'qualification', 'proposal', 'negotiation', 'won', 'lost']),
            'outreach-emails.status' => self::pairs(['draft', 'ready', 'approved', 'sent', 'replied', 'bounced', 'do_not_contact']),
            'whatsapp-outreaches.status' => self::pairs(['draft', 'opened', 'sent', 'replied', 'unavailable', 'do_not_contact']),
            'crm-tasks.status' => self::pairs(['todo', 'in_progress', 'done', 'cancelled']),
            'crm-tasks.priority', 'support-tickets.priority' => self::pairs(['low', 'normal', 'high', 'urgent']),
            'activities.type' => self::pairs(['call', 'email', 'whatsapp', 'meeting', 'note']),
            'client-projects.status' => self::pairs(['planned', 'active', 'on_hold', 'completed', 'cancelled']),
            'client-projects.billing_type' => self::pairs(['fixed', 'hourly', 'recurring']),
            'support-tickets.status' => self::pairs(['open', 'in_progress', 'waiting', 'resolved', 'closed']),
            'client-documents.type' => self::pairs(['file', 'contract', 'brief', 'credential', 'link']),
            'quotes.status' => self::pairs(['draft', 'sent', 'accepted', 'declined', 'expired']),
            'invoices.status' => self::pairs(['draft', 'sent', 'part_paid', 'paid', 'overdue', 'void']),
            'maintenance-plans.status' => self::pairs(['active', 'paused', 'cancelled', 'ended']),
            'contact-messages.status' => self::pairs(['new', 'replied', 'archived', 'spam']),
            'projects.accent' => ['blue' => 'Blue', 'cyan' => 'Cyan'],
            default => in_array($field, ['currency'], true) ? ['LKR' => 'LKR', 'USD' => 'USD'] : null,
        };
    }

    public static function fieldType(string $field, string $dbType): string
    {
        if (in_array($field, ['image_path', 'profile_photo'], true)) {
            return 'image';
        }
        if ($field === 'gallery') {
            return 'images';
        }
        if ($field === 'file_path') {
            return 'file';
        }
        if (in_array($field, ['tags', 'technologies'], true)) {
            return 'tags';
        }
        if (in_array($field, ['body', 'description', 'notes', 'details', 'message', 'summary', 'challenge', 'solution', 'responsibilities', 'scope', 'services', 'about_body', 'hero_intro', 'contact_body'], true)) {
            return 'textarea';
        }
        if (str_starts_with($dbType, 'timestamp') || str_contains($field, '_at') && ! in_array($field, ['awarded_at', 'started_at'], true)) {
            return 'datetime-local';
        }
        if ($dbType === 'date' || str_ends_with($field, '_date') || in_array($field, ['awarded_at', 'started_at', 'ended_at', 'deadline', 'valid_until'], true)) {
            return 'date';
        }
        if (str_contains($dbType, 'int') || in_array($field, ['amount', 'fee', 'total', 'tax', 'subtotal', 'monthly_fee', 'estimated_value', 'amount_paid', 'probability'], true)) {
            return 'number';
        }
        if (str_contains($dbType, 'bool') || str_starts_with($field, 'is_')) {
            return 'checkbox';
        }
        if ($field === 'email' || str_ends_with($field, '_email')) {
            return 'email';
        }
        if (str_ends_with($field, '_url') || in_array($field, ['url', 'website'], true)) {
            return 'url';
        }
        if ($field === 'phone') {
            return 'tel';
        }

        return 'text';
    }

    public static function label(string $field): string
    {
        return str($field)->replace('_id', '')->replace('_', ' ')->headline()->toString();
    }

    private static function pairs(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($value) => [$value => str($value)->replace('_', ' ')->headline()->toString()])->all();
    }
}
