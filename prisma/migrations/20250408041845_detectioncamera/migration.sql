BEGIN TRY

BEGIN TRAN;

-- CreateTable
CREATE TABLE [dbo].[DetectionLogs] (
    [id] INT NOT NULL IDENTITY(1,1),
    [foldername] NVARCHAR(1000),
    [linesendstatus] BIT NOT NULL CONSTRAINT [DetectionLogs_linesendstatus_df] DEFAULT 0,
    [picstatus] BIT NOT NULL CONSTRAINT [DetectionLogs_picstatus_df] DEFAULT 0,
    [vdostatus] BIT NOT NULL CONSTRAINT [DetectionLogs_vdostatus_df] DEFAULT 0,
    [createdAt] DATETIME2 NOT NULL CONSTRAINT [DetectionLogs_createdAt_df] DEFAULT CURRENT_TIMESTAMP,
    [updatedAt] DATETIME2 NOT NULL,
    CONSTRAINT [DetectionLogs_pkey] PRIMARY KEY CLUSTERED ([id])
);

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
