<?php

namespace TestMonitor\Jira\Builders\JQL;

/**
 * @see https://support.atlassian.com/jira-software-cloud/docs/advanced-search-reference-jql-fields
 */
class Field
{
    public const string AFFECTED_VERSION = 'affectedVersion';

    public const string APPROVALS = 'approvals';

    public const string ASSIGNEE = 'assignee';

    public const string ATTACHMENTS = 'attachments';

    public const string CATEGORY = 'category';

    public const string CHANGE_GATING_TYPE = 'change-gating-type';

    public const string COMMENT = 'comment';

    public const string COMPONENT = 'component';

    public const string CREATED = 'created';

    public const string CREATOR = 'creator';

    public const string DESCRIPTION = 'description';

    public const string DUE = 'due';

    public const string ENVIRONMENT = 'environment';

    public const string FILTER = 'filter';

    public const string FIX_VERSION = 'fixVersion';

    public const string HIERARCHY_LEVEL = 'hierarchyLevel';

    public const string ISSUE_KEY = 'issueKey';

    public const string ISSUE_LINK = 'issueLink';

    public const string ISSUE_LINK_TYPE = 'issueLinkType';

    public const string LABELS = 'labels';

    public const string LAST_VIEWED = 'lastViewed';

    public const string LEVEL = 'level';

    public const string ORGANIZATION = 'organizations';

    public const string ORIGINAL_ESTIMATE = 'originalEstimate';

    public const string PARENT = 'parent';

    public const string PARENT_SPACE = 'parentSpace';

    public const string PRIORITY = 'priority';

    public const string PROJECT = 'project';

    public const string PROJECT_TYPE = 'projectType';

    public const string REMAINING_ESTIMATE = 'remainingEstimate';

    public const string REPORTER = 'reporter';

    public const string REQUEST_CHANNEL_TYPE = 'request-channel-type';

    public const string REQUEST_LAST_ACTIVITY_TIME = 'request-last-activity-time';

    public const string REQUEST_TYPE = '"Request Type"';

    public const string RESOLUTION = 'resolution';

    public const string RESOLVED = 'resolved';

    public const string SPACE_TYPE = 'spaceType';

    public const string SPRINT = 'sprint';

    public const string STATUS = 'status';

    public const string SUMMARY = 'summary';

    public const string TEXT = 'text';

    public const string TEXT_FIELDS = 'textfields';

    public const string TIME_SPENT = 'timeSpent';

    public const string TYPE = 'type';

    public const string UPDATED = 'updated';

    public const string VOTER = 'voter';

    public const string VOTES = 'votes';

    public const string WATCHER = 'watcher';

    public const string WATCHERS = 'watchers';

    public const string WORK_ITEM_KEY = 'workItemKey';

    public const string WORK_ITEM_LINK = 'workItemLink';

    public const string WORK_ITEM_LINK_TYPE = 'workItemLinkType';

    public const string WORKLOG_COMMENT = 'worklogComment';

    public const string WORKLOG_DATE = 'worklogDate';

    public const string WORK_RATIO = 'workRatio';
}
