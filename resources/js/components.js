import mainComponent from './components/admin/main/MainComponent.vue';
import searchComponent from './components/admin/old_regulation/search/SearchComponent.vue';
// import teacherInfosComponent from './components/admin/search/teacher_infos/TeacherInfosComponent.vue';
import teacherInfosComponent from './components/admin/teachers/teacher_infos/TeacherInfosComponent.vue';

/* User */
import userComponent from './components/admin/user/IndexComponent.vue';

/* Document Management*/
import docMngCategoryComponent from './components/admin/document_mng/category/IndexComponent.vue';
import docMngListComponent from './components/admin/document_mng/list/IndexComponent.vue';
import docMngCommentComponent from './components/admin/document_mng/comment/IndexComponent.vue';
import docMngDocumentComponent from './components/admin/document_mng/document/IndexComponent.vue';
import docMngCreateDocumentComponent from './components/admin/document_mng/document/CreateComponent.vue';
import docMngEditDocumentComponent from './components/admin/document_mng/document/EditComponent.vue';
import docMngManualCreateDocumentComponent from './components/admin/document_mng/document/ManualCreateComponent.vue';
import docMngSearchComponent from './components/admin/document_mng/search/SearchComponent.vue';
import docMngReportComponent from './components/admin/document_mng/report/IndexComponent.vue';

/* Lawsuit Management*/
import lawsuitMngLawsuitsComponent from './components/admin/lawsuit_mng/lawsuits/IndexComponent';
import lawsuitMngStatisticalComponent from './components/admin/lawsuit_mng/statistical/IndexComponent';

/* Teachers */
import teachersComponent from './components/admin/teachers/IndexComponent';
import teachersPreviewComponent from './components/admin/teachers/TeachersPreviewComponent';
import teachMngInstitutionsComponent from './components/admin/institutions/IndexComponent';

/* Unions */
import unionsMngComponent from './components/admin/unions/IndexComponent';

/* Logs */
import logsComponent from './components/admin/logs/IndexComponent';

/* Settings */
import settingsComponent from './components/admin/settings/IndexComponent';

/* My Settings */
import mySettingsComponent from './components/admin/my_settings/IndexComponent';

export default {
	'main-component': mainComponent,
	'search-component': searchComponent,
	'teacher-infos-component': teacherInfosComponent,

	'user-component': userComponent,

	'doc-mng-category-component': docMngCategoryComponent,
	'doc-mng-list-component': docMngListComponent,
	'doc-mng-comment-component': docMngCommentComponent,
	'doc-mng-document-component': docMngDocumentComponent,
	'doc-mng-create-document-component': docMngCreateDocumentComponent,
	'doc-mng-edit-document-component': docMngEditDocumentComponent,
	'doc-mng-manual-create-document-component': docMngManualCreateDocumentComponent,
	'doc-mng-search-component': docMngSearchComponent,
	'doc-mng-report-component': docMngReportComponent,

	'teachers-component': teachersComponent,
	'teachers-preview-component': teachersPreviewComponent,
	'teach-mng-institutions-component': teachMngInstitutionsComponent,

	'unions-mng-component': unionsMngComponent,

	'lawsuit-mng-lawsuits-component': lawsuitMngLawsuitsComponent,
	'lawsuit-mng-statistical-component': lawsuitMngStatisticalComponent,

	'logs-component': logsComponent,

	'settings-component': settingsComponent,

	'my-settings-component': mySettingsComponent,
}
